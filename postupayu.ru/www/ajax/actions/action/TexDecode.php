<?php

class TexDecode extends AbstractAjaxAction {

    protected function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    protected function isCheckActivity() {
        return false;
    }

    protected function getRequiredParamKeys() {
        return 'hash';
    }

    protected function executeImpl(ArrayAdapter $params) {
        $hash = $params->str('hash');
        $tex = TexImager::inst()->decodeTexFromHash($hash);
        return $tex ? new AjaxSuccess($tex) : "Нет формулы с хэш-кодом [$hash]";
    }

}

?>