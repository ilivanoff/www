<?php

class CB_training extends BaseClientBox {

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    protected function getClientBoxFilling() {
        return new ClientBoxFilling('Последние занятия:', false, BASE_PAGE_TRAININGS);
    }

}

?>