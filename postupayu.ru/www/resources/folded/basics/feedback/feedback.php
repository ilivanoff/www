<?php

class BP_feedback extends BasicPage {

    public static function registerWebPages() {
        WebPages::register('feedback.php', 'Обратная связь', BASE_PAGE_FEEDBACK, PB_basic::getIdent(), AuthManager::AUTH_TYPE_NO_MATTER);
    }

    public function doProcess(RequestArrayAdapter $params) {
        
    }

    public function buildContent() {
        echo $this->getFoldedEntity()->fetchTpl();
    }

    public function getJsParams() {
        
    }

    public function getSmartyParams4Resources() {
        
    }

}

?>