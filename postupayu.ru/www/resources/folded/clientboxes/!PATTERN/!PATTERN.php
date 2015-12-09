<?php

class CB_pattern extends BaseClientBox {

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    protected function getClientBoxFilling() {
        return new ClientBoxFilling('Заголовок страницы');
    }

}

?>