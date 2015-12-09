<?php

class CB_adminoffice extends BaseClientBox {

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_AUTHORIZED_AS_ADMIN;
    }

    protected function getClientBoxFilling() {
        return new ClientBoxFilling('Администратор', true, PAGE_ADMIN);
    }

}

?>