<?php

class BP_magazine extends BasicPage {

    public static function registerWebPages() {
        WebPages::register('magazine.php', 'Журнал', BASE_PAGE_MAGAZINE, PB_basic::getIdent(), AuthManager::AUTH_TYPE_NO_MATTER);
    }

    public function doProcess(RequestArrayAdapter $params) {
        
    }

    public function buildContent() {
        $im = MagManager::inst();
        echo $this->getFoldedEntity()->fetchTpl(array('has_posts' => $im->hasPosts()));
    }

    public function getJsParams() {
        
    }

    public function getSmartyParams4Resources() {
        
    }

}

?>