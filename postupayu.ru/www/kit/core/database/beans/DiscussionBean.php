<?php

/**
 * Базовый бин для работы с дискуссиями.
 * Содержит весь необходимый базовый функционал.
 *
 * @author azazello
 */
final class DiscussionBean extends BaseBean {

    private $settings;
    private $table;
    private $idColumn;
    private $threadIdColumn;
    private $themeColumn;
    private $msgClass;

    public function __construct(DiscussionSettings $settings) {
        $this->settings = $settings;
        $this->table = $settings->getTable();
        $this->idColumn = $settings->getIdColumn();
        $this->threadIdColumn = $settings->getThreadIdColumn();
        $this->themeColumn = $settings->getThemeColumn();
        $this->msgClass = $settings->getMsgClass();
    }

    /** @return DiscussionMsg */
    public function getMsgById($msgId) {
        return new $this->msgClass($this->settings, $this->getRecEnsure("select * from $this->table where $this->idColumn=?", $msgId));
    }

    /**
     * Метод загружает сообщения из базы сразу в виде объектов
     */
    private function loadMessages($query, $inputarr = null) {
        return $this->getObjects($query, $inputarr, $this->msgClass, $this->idColumn, array($this->settings));
    }

    /**
     * Метод проверяет, есть ли у сообщения потомки
     */
    private function hasChilds($msgId) {
        return $this->getCnt("select count(1) as cnt from $this->table where id_parent is not null and id_parent=?", $msgId) > 0;
    }

    /**
     * Метод переносит содержимое сообщения в таблицу ps_discussion_backup перед его удалением.
     */
    private function backupMsg($msgId) {
        $themeCol = $this->themeColumn ? $this->themeColumn : 'null';
        $threadCol = $this->threadIdColumn ? $this->threadIdColumn : 'null';
        $this->update("INSERT INTO ps_discussion_backup
(v_table, id_msg, id_parent, id_root, id_thread, id_user, id_user_to, id_user_delete, dt_event, dt_event_delete, n_deep, theme, content)
SELECT ?, $this->idColumn, id_parent, id_root, $threadCol, id_user, id_user_to, ?, dt_event, unix_timestamp(), n_deep, $themeCol, content
  FROM   $this->table WHERE  $this->idColumn = ?", array(
            $this->table,
            AuthManager::getUserId(),
            $msgId
        ));
    }

    /**
     * После физического удаления сообщения методом deleteMsg вызывается данный метод для попытки удалить предка.
     * Если сам предок удалён и у него нет не удалённых потомков, то предок также будет удалён физически.
     * 
     * @param DiscussionMsg $msg - сообщение, удалённое физически
     */
    private function tryCleanParentOf(DiscussionMsg $msg) {
        if ($msg->isRoot()) {
            return; //---
        }
        if ($this->hasChilds($msg->getParentId())) {
            return; //---
        }
        $parent = $this->getMsgById($msg->getParentId());
        if (!$parent->isDeleted()) {
            return; //---
        }
        $this->update("delete from $this->table where $this->idColumn=?", $parent->getId());

        $this->tryCleanParentOf($parent);
    }

    /**
     * Основной метод удаления сообщения
     */
    public function deleteMsg(DiscussionMsg $msg) {
        check_condition(!$msg->isDeleted(), 'Massage is already deleted');
        //Выделим ряд переменных
        $msgId = $msg->getId();
        //Перенесём все данные в хранилище
        $this->backupMsg($msgId);
        //Вернём результат удаления
        $result['known'] = array();

        //Если есть потомки, то можем только обновить содержимое на null
        if ($this->hasChilds($msgId)) {
            $theme = $this->themeColumn ? ", $this->themeColumn=null" : '';
            $this->update("update $this->table set b_deleted=1, b_known=1, b_confirmed=1, content=null $theme where $this->idColumn=?", $msgId);
            $result['known'] = $this->markMsgChildsAsKnownDb($msgId, $msg->getUser()->getId());
            return $result; //---
        }

        //Удаляем комментарий физически
        $this->update("delete from $this->table where $this->idColumn=?", $msgId);

        //Проверим, нельзя ли физически удалить предка данного сообщения
        $this->tryCleanParentOf($msg);

        return $result;
    }

    /**
     * Мы сами получаем код следующего корневого сообщения. Нужно это по двум причинам:
     * 1. Мы не можем использовать в инсёрте сгенерированное значение для автоинкремент поля,
     *    а нам очень хочется иметь id_root not null.
     * 2. Нам хотелось бы сохранить увеличивающуюся нумерацию сообщений, даже в том случае,
     *    когда последнее корневое сообщение было физически удалено и перенесено в хранилище.
     */
    private function getNextRootId() {
        $query = "select MAX(id) as ID from
       (SELECT AUTO_INCREMENT as id FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?
        union
        SELECT IFNULL(MAX($this->idColumn), 0) as ID FROM $this->table) t";

        return 1 + (1 * array_get_value('ID', $this->getRecEnsure($query, $this->table)));
    }

    /**
     * Основной метод сохранения сообщения в базу
     * 
     * @return DiscussionMsg
     */
    public function saveMsg($threadId, $text, $theme, $templateId, $templateData, PsUser $author, PsUser $userTo = null, DiscussionMsg $parent = null) {
        //Строим запрос
        $insert = array();
        $params = array();

        //Код родителя
        if ($parent) {
            $insert['id_parent'] = '?';
            $params[] = $parent->getId();
        }

        //Пользователь, написавший сообщение
        $insert['id_user'] = '?';
        $params[] = $author->getId();

        //Пользователь, которому предназначено это сообщение
        $insert['id_user_to'] = '?';
        $params[] = $userTo ? $userTo->getId() : null;

        //Глубина сообщения
        $insert['n_deep'] = '?';
        $params[] = $parent ? $parent->getDeep() + 1 : 1;

        //Известен ли комментарий пользователю
        $insert['b_known'] = '?';
        $params[] = $userTo && !$userTo->isIt($author) ? 0 : 1;

        //Подтверждён ли комментарий админом (только если сообщение пишет админ или сообщение написано от имени дефолтного администратора)
        $insert['b_confirmed'] = '?';
        $params[] = $author->isAuthorisedAsAdmin() || PsUser::defaultAdmin()->isIt($author) ? 1 : 0;

        if (is_integer($templateId)) {
            $insert['id_template'] = '?';
            $params[] = $templateId;

            $insert['v_template'] = '?';
            $params[] = $templateData;
        } else {
            //Тема. Если есть предок - тема не нужна
            if (!$parent && $this->themeColumn) {
                $insert[$this->themeColumn] = '?';
                $params[] = $theme;
            }

            //Содержимое
            $insert['content'] = '?';
            $params[] = $text;
        }

        //entityId
        if ($this->threadIdColumn) {
            $insert[$this->threadIdColumn] = '?';
            $params[] = $threadId;
        }

        //date
        $insert['dt_event'] = 'UNIX_TIMESTAMP()';

        //Ссылка на root
        $msgId = null;

        if ($parent) {
            $insert['id_root'] = '?';
            $params[] = $parent->getRootId();
        } else {
            $msgId = $this->getNextRootId();

            $insert['id_root'] = '?';
            $params[] = $msgId;

            $insert[$this->idColumn] = '?';
            $params[] = $msgId;
        }

        $query = 'insert into ' . $this->table . ' (' . implode(', ', array_keys($insert)) . ') values (' . implode(', ', $insert) . ')';

        if ($msgId) {
            $this->update($query, $params);
        } else {
            $msgId = $this->insert($query, $params);
        }

        //Если мы писали ответ на сообщение, написанное нам - отметим, что мы знаем об этом сообщении
        if ($parent && !$parent->isKnown() && $parent->isToUser($author)) {
            $this->update("update $this->table set b_known=1 where $this->idColumn=? and id_user_to=?", array($parent->getId(), $author->getId()));
        }

        //Если админ отвечает на какое-то сообщение, то оно считается подтверждённым
        if ($parent && !$parent->isConfirmed() && $author->isAuthorisedAsAdmin()) {
            $this->update("update $this->table set b_confirmed=1 where $this->idColumn=?", $parent->getId());
        }

        return $this->getMsgById($msgId);
    }

    /**
     * Метод загружает порцию сообщений - используется как для построения дерева, так и для дозагрузки сообщений в ветку.
     */
    public function loadMsgsPortion($threadId = null, $maxCount = -1, $upDown = true, $rootId = null, &$bHasMore = false) {
        $where = array();
        if (is_inumeric($rootId)) {
            $where[] = Query::assocParam('id_root', $rootId, true, $upDown ? '>' : '<');
        }
        if ($this->threadIdColumn) {
            $where[] = "$this->threadIdColumn is not null";
            $where[$this->threadIdColumn] = $threadId;
        }
        $orderRoot = 'id_root ' . ($upDown ? 'asc' : 'desc');
        $orderMsgs = array('dt_event asc', $this->idColumn . ' asc');
        $order = array($orderRoot, $orderMsgs);

        $limit = null;
        if ($maxCount > 0) {
            $groups = $this->getArray(Query::select('count(1) as cnt, id_root', $this->table, $where, 'id_root', $orderRoot, $maxCount + 1));
            $groupNum = 0;
            $groupsCnt = count($groups);
            foreach ($groups as $group) {
                ++$groupNum;
                //$rootId = $group['id_root'];
                $limit = ($limit ? $limit : 0) + (int) $group['cnt'];
                if ($limit >= $maxCount) {
                    $bHasMore = $groupsCnt > $groupNum;
                    break;
                }
            }
        }

        //Мы вычислили лимит, теперь можно и загрузить сообщения
        return $this->loadMessages(Query::select('*', $this->table, $where, null, $order, $limit));
    }

    /*
     * ===================================
     * = ИЗВЕСТНОЕ/НЕИЗВЕСТНОЕ СООБЩЕНИЕ =
     * ===================================
     */

    /**
     * Возвращает кол-во сообщений, написанных пользователю, о которых он ещё не знает
     */
    public function getUserUnknownMsgsCntDb($userToId, $threadId = null) {
        $params = array($userToId);
        $AND_THREAD = '';

        if (is_numeric($threadId)) {
            $params[] = $threadId;
            $AND_THREAD = "AND $this->threadIdColumn = ?";
        }

        $query = "SELECT count(1) as cnt
  FROM $this->table
 WHERE     b_deleted = 0
       AND id_user_to is not null
       AND id_user_to <> id_user
       AND id_user_to = ?
       AND b_known = 0
       $AND_THREAD";

        return $this->getCnt($query, $params);
    }

    /**
     * Метод загружает все сообщения, адресованные пользователю, и ближайшую ветку сообщений к ним.
     * Возможны два варианты:
     * 1. Корневое сообщение написано пользователю (как в обратной связи)
     * 2. Пользователи пишут сообщения в ответ на сообщение пользователя
     */
    public function getUserUnknownMsgsDb($userToId, $threadId = null) {
        $params = array($userToId);
        $AND_THREAD = '';

        if (is_numeric($threadId)) {
            $params[] = $threadId;
            $AND_THREAD = "AND i.$this->threadIdColumn = ?";
        }

        $query = "SELECT *
  FROM $this->table c
 WHERE     c.b_deleted = 0
       AND c.$this->idColumn in
              (SELECT ifnull(id_parent, $this->idColumn)
                 FROM $this->table i
                WHERE     i.b_deleted = 0
                      AND i.id_user_to is not null
                      AND i.id_user_to <> i.id_user
                      AND i.id_user_to = ?
                      $AND_THREAD
                      AND i.b_known = 0)
ORDER BY c.dt_event ASC, c.$this->idColumn ASC";

        $msgs = $this->loadMessages($query, $params);

        $querySubtree = "SELECT *
  FROM $this->table c
 WHERE     b_deleted = 0
       AND id_parent is not null
       AND id_parent = ?
       AND id_user_to is not null
       AND id_user_to <> id_user
       AND id_user_to = ?
       AND b_known = 0
ORDER BY dt_event ASC, $this->idColumn ASC";

        /* @var $msg DiscussionMsg */
        foreach ($msgs as $msg) {
            //Подгружаем чаелдов только тогда, когда сообщение написано в ответ нам
            if ($msg->getUser()->isIt($userToId)) {
                foreach ($this->loadMessages($querySubtree, array($msg->getId(), $userToId)) as $child) {
                    $msg->addChild($child);
                }
            }
        }

        return $msgs;
    }

    /**
     * Метод помечает сообщение, как прочитанное
     */
    public function markMsgAsKnownDb(DiscussionMsg $msg) {
        if (!$msg->isKnown()) {
            $this->update("update $this->table set b_known=1 where $this->idColumn=? and id_user_to is not null and id_user_to=?", array($msg->getId(), $msg->getUserTo()->getId()));
            $msg->b_known = 1;
        }
    }

    /**
     * Метод помечает потомков сообщения, как прочитанные.
     * Возвращены будут коды сообщений, отмеченных, как прочтённые.
     */
    public function markMsgChildsAsKnownDb($msgId, $userId) {
        $where = 'where b_known=0 and id_parent is not null and id_parent=? and id_user_to is not null and id_user_to=?';
        $ids = $this->getIds("select $this->idColumn as id from $this->table $where", array($msgId, $userId));
        $this->update("update $this->table set b_known=1 $where", array($msgId, $userId));
        return $ids;
    }

    public function markUserUnknownMsgsAsKnownDb($userId, $threadId = null) {
        $params = array($userId);
        $AND_THREAD = '';

        if (is_numeric($threadId)) {
            $params[] = $threadId;
            $AND_THREAD = "AND $this->threadIdColumn = ?";
        }


        $this->update("update $this->table
   set b_known = 1
 where b_known = 0
   and id_user_to is not null
   and id_user_to = ? $AND_THREAD", $params);
    }

    /*
     * =============================================
     * = ПОДТВЕРЖДЁННОЕ/НЕПОДТВЕРЖДЁННОЕ СООБЩЕНИЕ =
     * =============================================
     */

    /**
     * Метод подтверждения сообщения
     */
    public function confirmMsg(DiscussionMsg $msg) {
        if (!$msg->isConfirmed()) {
            $this->update("update $this->table set b_confirmed=1 where $this->idColumn=?", $msg->getId());
            $msg->b_confirmed = 1;
        }
    }

    /**
     * Метод загружает ко-во неподтверждённых сообщений в треде
     */
    public function getNotConfirmemMsgsCntDb($threadId = null) {
        $AND_THREAD = is_numeric($threadId) ? "and $this->threadIdColumn = ?" : '';
        return $this->getCnt("select count(1) as cnt from $this->table where b_confirmed=0 and b_deleted=0 $AND_THREAD", $threadId);
    }

    /*
     * ====================
     * = УТИЛИТНЫЕ МЕТОДЫ =
     * ====================
     */

    /**
     * Метод возвращает содержимое всех комментариев, содержащих TeX формулы
     */
    public function getMsgsContentWithTex($threadId) {
        $where[] = '(content like "%\\\\\\\\[%" or content like "%\\\\\\\\(%")';
        $where[] = '(content like "%\\\\\\\\]%" or content like "%\\\\\\\\)%")';
        if ($threadId) {
            $where[$this->threadIdColumn] = $threadId;
        }
        return $this->getValues(Query::select('distinct content as value', $this->table, $where));
    }

}

?>