<?php

/**
 * Сообщение обратной связи от ананимного пользователя
 *
 * @author azazello
 */
class AnonimFeedback extends BaseDataStore {

    public function getId() {
        return $this->id_feedback;
    }

    public function getUserName() {
        return $this->user_name;
    }

    public function getContacts() {
        return $this->contacts;
    }

    public function getDtEvent($format = DF_COMMENTS) {
        return DatesTools::inst()->uts2dateInCurTZ($this->dt_event, $format);
    }

    public function getTheme() {
        return $this->theme;
    }

    public function getContent() {
        return $this->content;
    }

}

?>
