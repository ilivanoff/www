<?php

class CB_clientoffice extends BaseClientBox {

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_AUTHORIZED;
    }

    protected function getClientBoxFilling() {
        return new ClientBoxFilling('Вы авторизованы', true, PAGE_OFFICE);
    }

}

?>