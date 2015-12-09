<?php

class DialogWindowContent extends AbstractAjaxAction {

    protected function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    protected function isCheckActivity() {
        return false;
    }

    protected function getRequiredParamKeys() {
        return array('ident');
    }

    protected function executeImpl(ArrayAdapter $params) {
        return new AjaxSuccess(DialogManager::inst()->getDialog($params->str('ident'))->getWindowContent());
    }

}

?>