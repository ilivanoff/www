<?php

class UserToolsOrder extends AbstractAjaxAction {

    protected function getAuthType() {
        return AuthManager::AUTH_TYPE_AUTHORIZED;
    }

    protected function isCheckActivity() {
        return false;
    }

    protected function getRequiredParamKeys() {
        return array('states');
    }

    protected function executeImpl(ArrayAdapter $params) {
        $items = $params->arr('states');
        $res = PopupPagesManager::inst()->updateCurrentUserPagesOrder($items);
        return $res === true ? new AjaxSuccess() : $res;
    }

}

?>
