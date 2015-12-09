<?php

class MatchesAnsLoad extends AbstractAjaxAction {

    protected function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    protected function isCheckActivity() {
        return false;
    }

    protected function getRequiredParamKeys() {
        return array();
    }

    protected function executeImpl(ArrayAdapter $params) {
        $answers = $params->has('ident') ?
                MatchesManager::getInstance()->getAnswer4User($params->str('ident')) :
                MatchesManager::getInstance()->getAnswers4User();
        return new AjaxSuccess($answers);
    }

}

?>
