<?php

class ChessKnightAns extends AbstractAjaxAction {

    protected function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    protected function isCheckActivity() {
        return false;
    }

    protected function getRequiredParamKeys() {
        return array('hodes');
    }

    protected function executeImpl(ArrayAdapter $params) {
        ChessKnightManager::getInstance()->registerSolution($params->str('hodes'));
        return new AjaxSuccess();
    }

}

?>
