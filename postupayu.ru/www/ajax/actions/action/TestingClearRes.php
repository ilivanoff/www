<?php

class TestingClearRes extends AbstractAjaxAction {

    protected function getAuthType() {
        return AuthManager::AUTH_TYPE_AUTHORIZED;
    }

    protected function isCheckActivity() {
        return false;
    }

    protected function getRequiredParamKeys() {
        return array('id');
    }

    protected function executeImpl(ArrayAdapter $params) {
        $testingResId = $params->int('id');
        TestingManager::getInstance()->dropTestingResults($testingResId);
        return new AjaxSuccess();
    }

}

?>