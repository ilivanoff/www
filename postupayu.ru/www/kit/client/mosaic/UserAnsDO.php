<?php

/**
 * Description of UserBindDO
 *
 * @author azazello
 */
class UserAnsDO extends BaseDataStore {

    public function getId() {
        return $this->id_answer;
    }

    public function getAnswer() {
        return $this->v_answer;
    }

    //Только для подебителя.
//    var $id_user, $user_name;

    public function getUserId() {
        return $this->id_user;
    }

    public function getUserName() {
        return $this->user_name;
    }

}

?>