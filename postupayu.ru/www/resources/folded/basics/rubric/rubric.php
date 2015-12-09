<?php

class BP_rubric extends BasicPage {

    public static function registerWebPages() {
        WebPages::register('rubric.php', 'Все заметки раздела', PAGE_RUBRIC, PB_basic::getIdent(), AuthManager::AUTH_TYPE_NO_MATTER, BASE_PAGE_BLOG);
    }

    public function doProcess(RequestArrayAdapter $params) {
        
    }

    public function buildContent() {
        echo $this->getFoldedEntity()->fetchTpl(array('rubricCP' => BlogManager::inst()->getCurrentRubricContentProvider()));
    }

    public function getJsParams() {
        
    }

    public function getSmartyParams4Resources() {
        
    }

}

?>