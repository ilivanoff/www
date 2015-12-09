<?php

/**
 * Вспомогательные функции для построения дерева комментариев
 *
 * @author azazello
 */
abstract class DiscussionController {

    /** @var DiscussionSettings */
    private $SETTINGS;

    /** @var DiscussionBean */
    private $BEAN;

    /**
     * Параметры javascript, которые будут добавлены к дереву дискуссии для 
     * её построения и последующей передачи их обратно на сервер.
     */

    const JS_DATA_UPDOWN = 'updown'; //Направление дискуссии - сверху внизу или снизу вверх
    const JS_DATA_UNIQUE = 'unique'; //Уникальный идентификатор контроллера
    const JS_DATA_THREAD = 'thread'; //Код треда, в рамках которого идёт дискуссия
    const JS_DATA_THEMED = 'themed'; //Работает ли с темой

    //
    
    /**
     * Возвращает названия всех ключей, чтобы можно было проверить их наличие в запросе
     */
    public final static function getJsDataKeys() {
        return PsUtil::getClassConsts(__CLASS__, 'JS_DATA_');
    }

    /**
     * Действия, допустимые для комментария.
     * Их производят путём нажатия на кнопки-ссылки под комментарием.
     */

    const COMMENT_ACTION_KNOWN = 'known';
    const COMMENT_ACTION_DELETE = SmartyFunctions::ACTION_DELETE;
    const COMMENT_ACTION_CONFIRM = SmartyFunctions::ACTION_CONFIRM;
    const COMMENT_ACTION_LIKE = 'like';
    const COMMENT_ACTION_DISLIKE = 'dislike';
    const COMMENT_ACTION_UNVOTE = 'unvote';

    /**
     * Список всех действий над комментарием
     */
    public final static function getCommentActions() {
        return PsUtil::getClassConsts(__CLASS__, 'COMMENT_ACTION_');
    }

    /**
     * Действие - дозагрузка сообщений в дерево
     */

    const TREE_ACTION_LOAD_COMMENTS = 'load-comments';

    /**
     * Вместо того, чтобы писать массу protected методов, мы вынесем весь 
     * интересующий нас функционал в отдельный класс
     * 
     * @return DiscussionSettings настройки дискуссии
     */
    protected abstract function discusionSettings();

    /**
     * Уникальный идентификатор дискуссии, по которому происходит обращение к ней
     */
    public final function getDiscussionUnique() {
        return $this->SETTINGS->getUnique();
    }

    /**
     * Настройки дискуссии
     * 
     * @return DiscussionSettings
     */
    public final function getDiscussionSettings() {
        return $this->SETTINGS;
    }

    /**
     * Загрузка сообщения по его коду
     * @return DiscussionMsg
     */
    protected function getMsgById($msgId) {
        return $this->BEAN->getMsgById($msgId);
    }

    /**
     * Метод загругает порцию комментариев
     * 
     * @param type $threadId - код треда
     * @param type $maxCount - ограничение на максимальное кол-во загружаемых комментариев
     * @param type $upDown - направление загрузки - сверху вниз или наоборот
     * @param type $rootId - последний загруженный rootId. Параметр нужен для дозагрузки части комментариев
     * @param type $bHasMore - признак, есль ли ещё сообщения, которые можно дозагрузить
     * @return array - индексированный массив сообщений
     */
    private function loadMsgsPortion($threadId, $maxCount = -1, $upDown = true, $rootId = null, &$bHasMore = false) {
        return $this->BEAN->loadMsgsPortion($threadId, $maxCount, $upDown, $rootId, $bHasMore);
    }

    /**
     * Метод утверждает, что код треда - валиден, то есть данный тред доступен для обсуждения
     * и возможно выполнение действий над сообщениями из треда.
     */
    protected abstract function assertValidDiscussionEntityId($threadId);

    /**
     * Метод утверждает, что данное сообщение может быть сохранено.
     */
    protected abstract function assertCanSaveDiscussionMsg(PsUser $user, $parent = null, $threadId = null);

    /**
     * Возвращает код пользователя, для которого написано данное сообщение.
     */
    protected abstract function getIdUserTo4Root(PsUser $user, $threadId);

    /**
     * Метод валидирует $threadId согласно тому, работает ли данный менеджер дискуссии с тредами или нет
     */
    private function validateThreadId($threadId) {
        if ($this->SETTINGS->isWorkWithThreadId()) {
            check_condition($threadId, 'Не указан код треда');
            check_condition(is_inumeric($threadId), "Невалидный код треда: [$threadId]");
            $threadId = 1 * $threadId;
            check_condition(is_integer($threadId), "Нецелочисленный код треда: [$threadId]");
            $this->assertValidDiscussionEntityId($threadId);
        } else {
            check_condition(isEmpty($threadId), 'Указан код треда, хотя дискуссия ' . $this->getDiscussionUnique() . ' не работает с тредами');
            $threadId = null;
        }
        return $threadId;
    }

    /**
     * Метод получает на вход индексированный массив сообщений, а на выходе строит дерево дискуссии
     */
    private function makeDiscussionTree(array $msgs, $allowNotExistedParent) {
        $tree = array();

        /* @var $msg DiscussionMsg */
        foreach ($msgs as $msgId => $msg) {
            if ($msg->isRoot()) {
                $tree[$msgId] = $msg;
                continue; //Корневой элемент - он на своём месте
            }
            $parentId = $msg->getParentId();
            if (!array_key_exists($parentId, $msgs)) {
                if ($allowNotExistedParent) {
                    //Мы строим часть ветки - предка может и не быть
                    $tree[$msgId] = $msg;
                }
                continue; //
            }
            $msgs[$parentId]->addChild($msg);
        }

        return $tree;
    }

    /**
     * Аналог #buildLeaf, только на вход принимает id сообщения и всё.
     * ВАЖНО! Восновном метод используется в тестовых целях.
     * Обычно мы создаём ветку для имеющегося комментария.
     */
    public final function buildLeafById($msgId, $simple = false) {
        return $this->buildLeaf($this->getMsgById($msgId), $simple);
    }

    /**
     * Метод строит html листочка на дереве дискуссии:
     * 
     * <li>
     * ....
     *    <ul>
     *       <li>
     *       ....
     *       </li>
     *    </ul>
     * </li>
     * 
     * @param DiscussionMsg $msg
     */
    public final function buildLeaf(DiscussionMsg $msg, $simple = false) {
        $msgId = $msg->getId();
        $author = $msg->getUser();

        $isDeleted = $msg->isDeleted();
        $isUserComment = $author->isAuthorised();
        $isCanConfirm = !$isUserComment && !$msg->isConfirmed() && !$isDeleted && AuthManager::isAuthorizedAsAdmin();
        $notKnown = !$msg->isKnown() && $msg->getUserTo() && $msg->getUserTo()->isIt(PsUser::instOrNull());
        $msgUnique = $msg->getUnique();
        $threadUnique = $this->SETTINGS->getThreadUnique($msg->getThreadId());

        //Мы открываем страницу для показа этого комментария
        $liId = RequestArrayAdapter::inst()->str(GET_PARAM_GOTO_MSG) === $msgUnique ? " id=\"$msgUnique\"" : '';

        //Классы li
        $liClasses = array('msg');
        if ($isDeleted) {
            $liClasses[] = 'deleted';
        }

        //Классы Comment
        $divClasses = array('comment', $msgUnique);
        if ($isUserComment) {
            $divClasses[] = 'user_comment';
        }
        if ($isCanConfirm) {
            $divClasses[] = 'not_confirmed';
        }

        //Данные для Comment
        $divData['unique'] = $msgUnique;

        //Имя пользователя
        $userName = $author->getName() . ($isUserComment ? ' (вы)' : '');

        //КНОПКИ УПРАВЛЕНИЯ СВЕРХУ
        $controlsTop = array();
        if (!$simple) {
            //Кнопки управления сверху
            $controlsTop[] = PsHtml::a(array('href' => '#'), '#');
            if (!$msg->isRoot()) {
                $controlsTop[] = PsHtml::a(array('href' => '#', 'class' => 'parent'), '↑');
            }
        }

        //Кнопки голосования
        if ($this->SETTINGS->isVotable()) {
            $userCanVote = AuthManager::isAuthorized() && !$isUserComment;
            $votes = VotesManager::inst()->getVotesCount($threadUnique, $msgId);
            $likeCtrl = array();
            if ($userCanVote) {
                $vote = VotesManager::inst()->getUserVotes($threadUnique, $msgId, AuthManager::getUserId());
                $likeCtrl[] = PsHtml::a(array('class' => 'like ' . ($vote > 0 ? 'active' : 'clickable')), '+1');
                $likeCtrl[] = PsHtml::a(array('class' => 'votes' . ($vote == 0 ? '' : ' clickable') . ($votes == 0 ? '' : ($votes > 0 ? ' green' : ' red'))), abs($votes));
                $likeCtrl[] = PsHtml::a(array('class' => 'dislike ' . ($vote < 0 ? 'active' : 'clickable')), '−1');
            } else {
                $likeCtrl[] = PsHtml::a(array('class' => 'votes' . ($votes == 0 ? '' : ($votes > 0 ? ' green' : ' red'))), abs($votes));
            }
            $controlsTop[] = PsHtml::span(array('class' => 'vote'), implode('', $likeCtrl));
        }

        $controlsTop = implode('', $controlsTop);


        //КНОПКИ УПРАВЛЕНИЯ СНИЗУ
        $controlsBottom = '';
        if (!$simple && !$isDeleted) {
            $controlsBottom = array();
            $controlsBottom['id'] = $msgId;

            if ($isCanConfirm) {
                $controlsBottom[SmartyFunctions::ACTION_CONFIRM] = 'Принять';
            }

            if (!$msg->isMaxDeepLevel()) {
                $controlsBottom[SmartyFunctions::ACTION_REPLY] = 'Ответить';
            }

            if ($isUserComment) {
                $controlsBottom[SmartyFunctions::ACTION_DELETE] = 'Удалить';
            } elseif (AuthManager::isAuthorizedAsAdmin()) {
                $controlsBottom[SmartyFunctions::ACTION_DELETE] = 'Удалить (админ)';
            }

            $controlsBottom = SmartyFunctions::psctrl($controlsBottom);
        }


        //Собираем параметры в кучу
        $params['msg'] = $msg;
        $params['new'] = $notKnown;
        $params['builder'] = $this;
        $params['liId'] = $liId;
        $params['liClasses'] = implode(' ', $liClasses);
        $params['divClasses'] = implode(' ', $divClasses);
        $params['divData'] = PsHtml::data2string($divData);
        $params['msgId'] = $msgId;
        $params['simple'] = $simple;
        $params['avatar'] = $author->getAvatarImg('42x', array('class' => 'small'));
        $params['userName'] = $userName;
        $params['controlsTop'] = $controlsTop;
        $params['controlsBottom'] = $controlsBottom;

        if ($msg->isTemplated()) {
            $params['msgCtt'] = TemplateMessages::inst()->decodeTemplateMsg($msg);
        } else {
            $params['msgCtt'] = $msg;
        }

        return PSSmarty::template('discussion/common/leaf.tpl', $params)->fetch();
    }

    /**
     * Строит листья дерева
     */
    private final function buildLeafs($threadId, array $msgs) {
        //Предзагрузим голосование за сомментарии
        VotesManager::inst()->enableCached($this->SETTINGS->getThreadUnique($threadId));

        $leafs = '';
        /* @var $msg DiscussionMsg */
        foreach ($msgs as $msg) {
            $leafs .= $this->buildLeaf($msg);
        }
        return $leafs;
    }

    /**
     * Оснвной метод построения дискуссии - с кнопками управления и т.д.
     */
    public final function buildDiscussion($upDown = true, $threadId = null, $limited = true) {
        $threadId = $this->validateThreadId($threadId);

        //Form html
        $formParams['avatar'] = PsUserHelper::getAvatarImg(PsUser::instOrNull(), '42x', array('class' => 'small'));
        $formParams['themed'] = $this->SETTINGS->isThemed();
        $form = PSSmarty::template('discussion/common/comment_form.tpl', $formParams)->fetch();

        //Tree js data
        $treeData[self::JS_DATA_UPDOWN] = $upDown;
        $treeData[self::JS_DATA_THREAD] = $threadId;
        $treeData[self::JS_DATA_UNIQUE] = $this->SETTINGS->getUnique();
        $treeData[self::JS_DATA_THEMED] = $this->SETTINGS->isThemed();


        $params['tree'] = $this->buildLeafs($threadId, $this->makeDiscussionTree($this->loadMsgsPortion($threadId, $limited ? MAX_COMMENTS_COUNT : -1, $upDown, null, $hasMore), false));
        $params['has_more'] = $hasMore;
        $params['form'] = $form;
        $params['unique'] = $this->SETTINGS->getUnique();
        $params['data'] = PsHtml::data2string($treeData);

        return PSSmarty::template('discussion/common/discussion.tpl', $params)->fetch();
    }

    /**
     * Построение простой дискуссии - для одного листа. Выполняет основные действия.
     */
    public final function buildDiscussionSimple(DiscussionMsg $leaf) {
        //Tree js data
        $treeData[self::JS_DATA_UPDOWN] = true;
        $treeData[self::JS_DATA_THREAD] = $leaf->getThreadId();
        $treeData[self::JS_DATA_UNIQUE] = $this->SETTINGS->getUnique();
        $treeData[self::JS_DATA_THEMED] = $this->SETTINGS->isThemed();


        $params['tree'] = $this->buildLeaf($leaf, true);
        $params['data'] = PsHtml::data2string($treeData);

        return PSSmarty::template('discussion/common/discussion_simple.tpl', $params)->fetch();
    }

    /**
     * Метод вызывается из Ajax для выполнения действия над комментарием
     */
    public final function executeCommentAction($msgId, $action) {
        $msg = $this->getMsgById($msgId);

        check_condition(!$msg->isDeleted(), 'Сообщение удалено');

        $this->validateThreadId($msg->getThreadId());

        $USER = PsUser::inst();

        $msgId = $msg->getId();
        $userId = $USER->getId();
        $authorId = $msg->getUser()->getId();

        $RESPONSE = '';

        switch ($action) {
            //УДАЛЕНИЕ
            case self::COMMENT_ACTION_DELETE:
                check_condition($USER->isAuthorisedAsAdmin() || $USER->isIt($authorId), 'Недостаточно прав');
                $RESPONSE = $this->BEAN->deleteMsg($msg);
                $RESPONSE['known'] = $this->convertMsgIdsToUniques($RESPONSE['known']);
                break;

            //ПОДТВЕРЖДЕНИЕ
            case self::COMMENT_ACTION_CONFIRM:
                check_condition($USER->isAuthorisedAsAdmin(), 'Недостаточно прав');
                $this->BEAN->confirmMsg($msg);
                if ($msg->isToUser($USER)) {
                    $this->BEAN->markMsgAsKnownDb($msg);
                }
                break;

            //ОТМЕТКА О ПРОЧИТАННОСТИ
            case self::COMMENT_ACTION_KNOWN:
                check_condition($msg->isToUser($USER), 'Это сообщение отправлено не вам');
                $this->BEAN->markMsgAsKnownDb($msg);
                if ($USER->isAuthorisedAsAdmin()) {
                    $this->BEAN->confirmMsg($msg);
                }
                break;
            case self::COMMENT_ACTION_LIKE:
            case self::COMMENT_ACTION_DISLIKE:
            case self::COMMENT_ACTION_UNVOTE:
                check_condition($this->SETTINGS->isVotable(), 'Голосование запрещено');
                check_condition(!$USER->isIt($authorId), 'Запрещено голосовать за своё сообщение');
                $threadUnique = $this->SETTINGS->getThreadUnique($msg->getThreadId());
                switch ($action) {
                    case self::COMMENT_ACTION_LIKE:
                        VotesManager::inst()->addVote($threadUnique, $msgId, $userId, $authorId, 1);
                        break;
                    case self::COMMENT_ACTION_DISLIKE:
                        VotesManager::inst()->addVote($threadUnique, $msgId, $userId, $authorId, -1);
                        break;
                    case self::COMMENT_ACTION_UNVOTE:
                        VotesManager::inst()->removeVote($threadUnique, $msgId, $userId);
                        break;
                }
                $RESPONSE = VotesManager::inst()->getVotesCount($threadUnique, $msgId);
                break;
            default:
                raise_error("Неизвестное действие [$action].");
        }

        return $RESPONSE;
    }

    /**
     * Имплементация метода, создающего сообщение
     * 
     * @return DiscussionMsg созданное сообщение
     */
    public final function saveMessageImpl($threadId, $parentId, $text, $theme, $templateId, $templateData, PsUser $author) {
        $threadId = $this->validateThreadId($threadId);

        $unique = $this->SETTINGS->getUnique();

        check_condition($templateId === null || is_integer($templateId), "Код шаблона [$templateId] должен быть целочисленным");
        check_condition($templateId === null || $this->SETTINGS->isTemplatable(), "Дискуссия [$unique] не работает с шаблонными сообщениями");

        /* @var $parent DiscussionMsg */
        $parent = is_numeric($parentId) ? $this->getMsgById($parentId) : null;

        $checkThreadId = $parent ? $this->validateThreadId($parent->getThreadId()) : $threadId;

        check_condition($threadId === $checkThreadId, "Не совпадают коды тредов: [$threadId]!=[$checkThreadId]");

        if ($parent) {
            check_condition(!$parent->isDeleted(), 'Родительское сообщение удалено.');
            check_condition(!$parent->isMaxDeepLevel(), 'Достигнут максимальный уровень вложенности.');
        }

        $this->assertCanSaveDiscussionMsg($author, $parent, $threadId);

        $idUserTo = $parent ? $parent->getUser()->getId() : AuthManager::validateUserIdOrNull($this->getIdUserTo4Root($author, $threadId));
        $userTo = is_integer($idUserTo) ? PsUser::inst($idUserTo, true) : null;

        return $this->BEAN->saveMsg($threadId, $text, $theme, $templateId, $templateData, $author, $userTo, $parent);
    }

    /**
     * Метод создаёт новое шаблонное сообщение
     * 
     * @return DiscussionMsg созданное сообщение
     */
    public final function saveTemplatedMessage($threadId, $parentId, $templateId, $templateData, PsUser $author) {
        return $this->saveMessageImpl($threadId, $parentId, null, null, $templateId, $templateData, $author);
    }

    /**
     * Метод создаёт новое сообщение
     * 
     * @return DiscussionMsg созданное сообщение
     */
    public final function saveMessage($threadId, $parentId, $text, $theme, PsUser $author) {
        return $this->saveMessageImpl($threadId, $parentId, $text, $theme, null, null, $author);
    }

    /**
     * Метод дозагружает часть дерева сообщений
     */
    public function loadTree($rootId, $upDown, $threadId) {
        $threadId = $this->validateThreadId($threadId);
        $tree = $this->makeDiscussionTree($this->loadMsgsPortion($threadId, MAX_COMMENTS_COUNT, $upDown, $rootId, $hasMore), false);
        $params['tree'] = $this->buildLeafs($threadId, $tree);
        $params['hasmore'] = $hasMore;
        return $params;
    }

    /**
     * Конвертирует массив кодов сообщений в массив уникальных идентификаторов
     */
    private function convertMsgIdsToUniques(array $msgIds) {
        $result = array();
        foreach ($msgIds as $msgId) {
            $result[] = $this->SETTINGS->getMsgUnique($msgId);
        }
        return $result;
    }

    /**
     * Метод загружает комментарии, содержащие TeX формулы
     * 
     * @param type $threadId - даже если менеджер работает с сущностями, мы не требуем её передачи
     */
    public function getMsgsContentWithTex($threadId = null) {
        $threadId = is_numeric($threadId) ? $this->validateThreadId($threadId) : null;
        return $this->BEAN->getMsgsContentWithTex($threadId);
    }

    public function getUserUnknownMsgsCnt($userId, $threadId = null) {
        $threadId = is_numeric($threadId) ? $this->validateThreadId($threadId) : null;
        return $this->BEAN->getUserUnknownMsgsCntDb($userId, $threadId);
    }

    public function getUserUnknownMsgs($userId, $threadId = null) {
        $threadId = is_numeric($threadId) ? $this->validateThreadId($threadId) : null;
        return $this->BEAN->getUserUnknownMsgsDb($userId, $threadId);
    }

    public function markMsgChildsAsKnown($msgId, $userId) {
        $this->BEAN->markMsgChildsAsKnownDb($msgId, $userId);
    }

    public function markUserMsgsAsKnown($userId, $threadId = null) {
        $threadId = is_numeric($threadId) ? $this->validateThreadId($threadId) : null;
        $this->BEAN->markUserUnknownMsgsAsKnownDb($userId, $threadId);
    }

    public function getNotConfirmemMsgsCnt($threadId = null) {
        AuthManager::checkAdminAccess();
        $threadId = is_numeric($threadId) ? $this->validateThreadId($threadId) : null;
        return $this->BEAN->getNotConfirmemMsgsCntDb($threadId);
    }

    function __construct() {
        $this->SETTINGS = $this->discusionSettings();
        $this->BEAN = new DiscussionBean($this->SETTINGS);
    }

}

?>