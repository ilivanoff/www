<?php

class TestAction extends AbstractAjaxAction {

    protected function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    protected function isCheckActivity() {
        return false;
    }

    protected function getRequiredParamKeys() {
        return array('method');
    }

    protected function executeImpl(ArrayAdapter $params) {
        $method = $params->str('method');
        $params = $params->arr('params');
        TestManagerCaller::execute($method, $params);
        return new AjaxSuccess();
    }

}

?>