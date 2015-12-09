<?php

/**
 * Дополнительные возможности для администратора по работе с лентой обратной связи.
 * Восновном нужно для работы с сообщениями обратной связи от анонимного пользователя.
 */
class AdminFeedbackBean extends BaseBean {

    public function getAnonimMsgsCnt() {
        return $this->getCnt('select count(1) as cnt from feedback where b_deleted=0');
    }

    public function getAnonimMsgs() {
        return $this->getObjects('select * from feedback where b_deleted=0 order by dt_event asc, id_feedback asc', null, 'AnonimFeedback');
    }

    public function deleteAnonimMsg($feedId) {
        $this->update('update feedback set b_deleted=1 where id_feedback=?', $feedId);
    }

    /** @return AdminFeedbackBean */
    public static function inst() {
        return parent::inst();
    }

}

?>