<?php

class BP_post extends BasicPage {

    public static function registerWebPages() {
        WebPages::register('post.php', 'Пост', PAGE_POST, PB_basic::getIdent(), AuthManager::AUTH_TYPE_NO_MATTER, BASE_PAGE_BLOG);
    }

    public function doProcess(RequestArrayAdapter $params) {
        
    }

    public function buildContent() {
        echo $this->getFoldedEntity()->fetchTpl(array('postCP' => BlogManager::inst()->getCurrentPostContentProvider()));
    }

    public function getJsParams() {
        
    }

    public function getSmartyParams4Resources() {
        
    }

}

?>