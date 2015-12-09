<?php

class CB_login extends BaseClientBox {

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_NOT_AUTHORIZED;
    }

    protected function getClientBoxFilling() {
        return new ClientBoxFilling('Авторизация');
    }

}

?>