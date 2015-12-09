<?php

class IP_stock extends BaseIdentPage {

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    public function getTitle() {
        return 'Акции';
    }

    protected function processRequest(\ArrayAdapter $params) {
        return new IdentPageContent(StockManager::inst()->stockPageHtml());
    }

}

?>