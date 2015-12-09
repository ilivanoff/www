<?php

class IP_pattern extends BaseIdentPage {

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    public function getTitle() {
        return 'Название загружаемой страницы';
    }

    protected function processRequest(\ArrayAdapter $params) {
        return new IdentPageFilling();
    }

}

?>