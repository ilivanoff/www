<?php

class PopupBean extends BaseBean {

    public function getUserFavorites($userId) {
        return $this->getArray('select * from ps_user_popups where id_user=? order by n_order asc, dt_event asc', $userId);
    }

    public function toggleUserPopup($userId, $isAdd, $type, $ident) {
        $params = array($userId, $type, $ident);
        if ($isAdd) {
            $this->update('insert into ps_user_popups (id_user, v_type, v_ident, n_order, dt_event) values (?, ?, ?, 9999, unix_timestamp())', $params);
        } else {
            $this->update('delete from ps_user_popups where id_user=? and v_type=? and v_ident=?', $params);
        }
    }

    public function saveUserPlugins($userId, array $items) {
        $this->update('delete from ps_user_popups where id_user=?', $userId);
        $num = 0;
        foreach ($items as $item) {
            $this->update('insert into ps_user_popups (id_user, v_type, v_ident, n_order, dt_event) values (?, ?, ?, ?, unix_timestamp())', array(
                $userId, $item['type'], $item['ident'], ++$num
            ));
        }
    }

    /*
     * СИНГЛТОН
     */

    /** @return PopupBean */
    public static function inst() {
        return parent::inst();
    }

}

?>
