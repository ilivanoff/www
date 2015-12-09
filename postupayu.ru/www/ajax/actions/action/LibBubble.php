<?php

class LibBubble extends AbstractAjaxAction {

    protected function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    protected function isCheckActivity() {
        return false;
    }

    protected function getRequiredParamKeys() {
        return array('unique');
    }

    protected function executeImpl(ArrayAdapter $params) {
        return new AjaxSuccess(PsBubble::extractFoldedEntityBubbleDiv($params->str('unique')));
    }

}

?>
