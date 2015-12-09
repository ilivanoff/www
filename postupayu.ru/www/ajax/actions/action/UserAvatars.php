<?php

class UserAvatars extends AbstractAjaxAction {

    protected function getAuthType() {
        return AuthManager::AUTH_TYPE_AUTHORIZED;
    }

    protected function isCheckActivity() {
        return false;
    }

    protected function getRequiredParamKeys() {
        
    }

    protected function executeImpl(ArrayAdapter $params) {
        return new AjaxSuccess(PsUser::inst()->getAvatarsList());
    }

}

?>