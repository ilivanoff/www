<?php

/**
 * Description of UtilsBean
 *
 * @author Admin
 */
class UtilsBean extends BaseBean {
    /*
     * =====================================
     * ======== ПРОСМОТРЫ СТРАНИЦ ==========
     * =====================================
     */

    public function savePageWatch($userId, $pageIdent) {
        $params[] = $pageIdent;

        $userRestriction = '';
        if ($userId) {
            $params[] = $userId;
            $userRestriction = '= ?';
        } else {
            $userRestriction = 'is null';
        }

        $affected = $this->update('
update page_watch
   set dt_event = UNIX_TIMESTAMP(), watch_count = watch_count + 1
 where page_ident = ? and id_user ' . $userRestriction, $params);

        if ($affected < 1) {
            $this->update('
insert into page_watch
  (id_user, page_ident, dt_event, watch_count)
values
  (?, ?, UNIX_TIMESTAMP(), 1)', array(
                $userId,
                $pageIdent));
        }
    }

    public function getLastPageWatch($user_id, $page_ident) {
        return $this->getInt('
select dt_event
  from page_watch
 where id_user is not null and id_user = ?
   and page_ident = ?', array($user_id, $page_ident));
    }

    //$userId can be null
    public function isPageWasOpened($url, $userId = null) {
        $params[] = $url;
        if ($userId === null) {
            $user = 'is null';
        } else {
            $user = '= ?';
            $params[] = $userId;
        }
        return $this->getCnt('select count(1) as cnt from page_watch where page_ident=? and id_user ' . $user, $params) > 0;
    }

    /*
     * =====================================
     * ===== АВТОРИЗАЦИИ ПОЛЬЗОВАТЕЛЯ ======
     * =====================================
     */

    public function saveAudit($parentId, $userId, $userIdAuthed, $processId, $action, $data, $encoded) {
        return $this->insert(
                        'INSERT INTO ps_audit (id_rec_parent, id_user, id_user_authed, id_process, dt_event, n_action, v_data, b_encoded) VALUES (?, ?, ?, ?, unix_timestamp(), ?, ?, ?)', //
                        array($parentId, $userId, $userIdAuthed, $processId, $action, $data, $encoded));
    }

    /*
     * =====================================
     * ============= ОПЕЧАТКИ ==============
     * =====================================
     */

    public function saveMisprint($url, $text, $note = null, $user_id = null) {
        $ident = md5("text: $text, note: $note");

        $cnt = $this->getCnt('select count(1) as cnt from ps_misprint where url=? and ident=?', array($url, $ident));
        if ($cnt > 0) {
            //Такая запись уже есть
            return false;
        }

        $updated = $this->update('INSERT INTO ps_misprint(url, text, note, id_user, ident) VALUES (?, ?, ?, ?, ?)', array(
            $url, $text, $note, $user_id, $ident));

        return $updated > 0;
    }

    public function getMissprints() {
        return $this->getArray('select * from ps_misprint where b_deleted=0 order by id_missprint limit 50');
    }

    public function removeMissprint($id) {
        $this->update('update ps_misprint set b_deleted=1 where id_missprint=?', array($id));
    }

    /**
     * Получение now() из базы (2014-04-07 16:44:33)
     */
    public function getDbNow() {
        return $this->getValue('select now()');
    }

    /**
     * Получение unix_timestamp() из базы (1396874703)
     */
    public function getDbUnixTimeStamp() {
        return $this->getInt('select unix_timestamp()');
    }

    /*
     * ====================================
     * ==== НАСТРОЙКИ, ХРАНИМЫЕ В БАЗЕ ====
     * ====================================
     */

    public function getDbProp($col, $name, $default) {
        return $this->getValue(Query::select($col, 'ps_props', array('v_prop' => $name)), null, $default);
    }

    public function setDbProp($col, $name, $val) {
        if ($this->hasRec('ps_props', array('v_prop' => $name))) {
            $this->update("update ps_props set $col=? where v_prop=?", array($val, $name));
        } else {
            $this->update("insert into ps_props ($col, v_prop) values (?, ?)", array($val, $name));
        }
    }

    public function delDbProp($name) {
        $this->update('delete from ps_props where v_prop=?', $name);
    }

    /** @return UtilsBean */
    public static function inst() {
        return parent::inst();
    }

}

?>