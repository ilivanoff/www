<?php

class IP_news extends BaseIdentPage {

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    public function getTitle() {
        return 'Лента новостей';
    }

    protected function processRequest(ArrayAdapter $params) {
        $newsLine = NewsManager::getInstance()->getNewsLine();
        return new IdentPageFilling(array('line' => $newsLine), array('states' => $newsLine['states']));
    }

}

?>