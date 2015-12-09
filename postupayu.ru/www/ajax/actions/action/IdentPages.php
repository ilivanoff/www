<?php

//Страницы, загружаемые с помошью JavaScript
class IdentPages extends AbstractAjaxAction {

    protected function getRequiredParamKeys() {
        return IDENT_PAGE_PARAM;
    }

    protected function isCheckActivity() {
        return false;
    }

    protected function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    protected function executeImpl(ArrayAdapter $params) {
        return new AjaxSuccess(IdentPagesManager::inst()->getCurPageContent()->toArray4Json());
    }

}

?>