<?php

class BP_blog extends BasicPage {

    public static function registerWebPages() {
        WebPages::register('blog.php', 'Блог', BASE_PAGE_BLOG, PB_basic::getIdent(), AuthManager::AUTH_TYPE_NO_MATTER);
    }

    public function doProcess(RequestArrayAdapter $params) {
        
    }

    public function buildContent() {
        $bm = BlogManager::inst();
        echo $this->getFoldedEntity()->fetchTpl(array('has_posts' => $bm->hasPosts()));
    }

    public function getJsParams() {
        
    }

    public function getSmartyParams4Resources() {
        
    }

}

?>