<?php

class StockAction extends AbstractAjaxAction {

    protected function getAuthType() {
        return AuthManager::AUTH_TYPE_AUTHORIZED;
    }

    protected function isCheckActivity() {
        return false;
    }

    protected function getRequiredParamKeys() {
        return array(STOCK_IDENT_PARAM, STOCK_ACTION_PARAM);
    }

    protected function executeImpl(ArrayAdapter $params) {
        return StockManager::inst()->executeAjaxAction($params);
    }

}

?>