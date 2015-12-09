<?php

class eclassname extends BasicPage {

    public static function registerWebPages() {
        //HtmlPages::register('mypage.php', 'Моя страница', PAGE_MYPAGE, PB_basic::getIdent(), AuthManager::AUTH_TYPE_NO_MATTER);
    }

    public function doProcess(RequestArrayAdapter $params) {
        
    }

    public function buildContent() {
        return $this->getFoldedEntity()->fetchTpl();
    }

    public function getJsParams() {
        
    }

    public function getSmartyParams4Resources() {
        
    }

}

?>
