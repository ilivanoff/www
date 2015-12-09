<?php

class BP_helpus extends BasicPage {

    public static function registerWebPages() {
        WebPages::register('helpus.php', 'Поддержать проект', PAGE_HELPUS, PB_basic::getIdent(), AuthManager::AUTH_TYPE_NO_MATTER, BASE_PAGE_FEEDBACK);
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