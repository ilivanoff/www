<?php

class BP_registration extends BasicPage {

    public static function registerWebPages() {
        WebPages::register('registration.php', 'Регистрация', PAGE_REGISTRATION, PB_basic::getIdent(), AuthManager::AUTH_TYPE_NOT_AUTHORIZED, BASE_PAGE_INDEX, PAGE_OFFICE);
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