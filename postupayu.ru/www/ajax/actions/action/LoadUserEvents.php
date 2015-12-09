
<?php

class LoadUserEvents extends AbstractAjaxAction {

    protected function getAuthType() {
        return AuthManager::AUTH_TYPE_AUTHORIZED;
    }

    protected function isCheckActivity() {
        return false;
    }

    protected function getRequiredParamKeys() {
        return array();
    }

    protected function executeImpl(ArrayAdapter $params) {
        return new AjaxSuccess(IdentPagesManager::inst()->getCurrentUserEvents());
    }

}

?>
