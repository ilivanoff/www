<?php

class LoadNews extends AbstractAjaxAction {

    protected function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    protected function isCheckActivity() {
        return false;
    }

    protected function getRequiredParamKeys() {
        return array('states');
    }

    protected function executeImpl(ArrayAdapter $params) {
        $states = $params->arr('states');
        $newsLine = NewsManager::getInstance()->getNewsLine($states);
        return new AjaxSuccess($newsLine);
    }

}

?>
