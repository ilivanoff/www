<?php

class IP_settings extends BaseIdentPage {

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    public function getTitle() {
        return 'Утилиты';
    }

    protected function processRequest(\ArrayAdapter $params) {
        return new IdentPageFilling();
    }

}

?>