<?php

class BP_map extends BasicPage {

    public static function registerWebPages() {
        WebPages::register('map.php', 'Карта сайта', BASE_PAGE_MAP, PB_basic::getIdent(), AuthManager::AUTH_TYPE_NO_MATTER);
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