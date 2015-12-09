<?php

/**
 * Description of ISBean
 *
 * @author Admin
 */
class AdminUserBean extends UserBean {

    public function getClientsCount() {
        return $this->getCnt('select count(1) as cnt from users where b_admin=0');
    }

    public function getClients() {
        $result = array();
        foreach ($this->getIds('select id_user as id from users where b_admin=0 order by id_user asc') as $userId) {
            $result[] = PsUser::inst($userId);
        }
        return $result;
    }

    /*
     * СИНГЛТОН
     */

    /** @return AdminUserBean */
    public static function inst() {
        return parent::inst();
    }

}

?>
