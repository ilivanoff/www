<?php

class PopupFavorites extends AbstractAjaxAction {

    protected function getAuthType() {
        return AuthManager::AUTH_TYPE_AUTHORIZED;
    }

    protected function isCheckActivity() {
        return false;
    }

    protected function getRequiredParamKeys() {
        return array('fav', 'type', 'ident');
    }

    protected function executeImpl(ArrayAdapter $params) {
        $fav = $params->bool('fav');
        $type = $params->str('type');
        $ident = $params->str('ident');

        PopupPagesManager::inst()->toggleUserPopup($fav, $type, $ident);

        return new AjaxSuccess();
    }

}

?>