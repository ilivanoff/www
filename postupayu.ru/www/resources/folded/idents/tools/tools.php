<?php

class IP_tools extends BaseIdentPage {

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    public function getTitle() {
        return 'Утилиты';
    }

    public function processRequest(ArrayAdapter $params) {
        return new IdentPageFilling(array('pages' => PopupPagesManager::inst()->getCurrentUserPagesList()));
    }

}

?>