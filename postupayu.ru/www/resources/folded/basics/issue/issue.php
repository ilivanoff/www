<?php

class BP_issue extends BasicPage {

    public static function registerWebPages() {
        WebPages::register('issue.php', 'Выпуск журнала', PAGE_ISSUE, PB_basic::getIdent(), AuthManager::AUTH_TYPE_NO_MATTER, BASE_PAGE_MAGAZINE);
    }

    public function doProcess(RequestArrayAdapter $params) {
        
    }

    public function buildContent() {
        echo $this->getFoldedEntity()->fetchTpl(array('postCP' => MagManager::inst()->getCurrentPostContentProvider()));
    }

    public function getJsParams() {
        
    }

    public function getSmartyParams4Resources() {
        
    }

}

?>
