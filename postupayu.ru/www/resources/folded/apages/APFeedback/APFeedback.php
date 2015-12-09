<?php

class AP_APFeedback extends BaseAdminPage {

    public function title() {
        $cnt = AdminFeedbackBean::inst()->getAnonimMsgsCnt();
        return 'Обратная связь' . ($cnt > 0 ? " ($cnt)" : '');
    }

    public function buildContent() {
        return $this->getFoldedEntity()->fetchTpl(array('msgs' => AdminFeedbackBean::inst()->getAnonimMsgs()));
    }

}

?>