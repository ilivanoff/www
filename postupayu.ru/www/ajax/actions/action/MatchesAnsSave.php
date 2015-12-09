<?php

class MatchesAnsSave extends AbstractAjaxAction {

    protected function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    protected function isCheckActivity() {
        return false;
    }

    protected function getRequiredParamKeys() {
        return array('ident', 'matches');
    }

    protected function executeImpl(ArrayAdapter $params) {
        $bindedToUser = MatchesManager::getInstance()->registerSolution($params->str('ident'), $params->str('matches'));
        return new AjaxSuccess($bindedToUser);
    }

}

?>
