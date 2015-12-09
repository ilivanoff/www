<?php

class BP_passrecover extends BasicPage {

    public static function registerWebPages() {
        WebPages::register('passrecover.php', 'Восстановление пароля', PAGE_PASS_REMIND, PB_basic::getIdent(), AuthManager::AUTH_TYPE_NOT_AUTHORIZED, BASE_PAGE_INDEX, PAGE_OFFICE);
    }

    public function doProcess(RequestArrayAdapter $params) {
        
    }

    public function buildContent() {
        $code = RequestArrayAdapter::inst()->str(REMIND_CODE_PARAM);
        $error = $code ? PassRecoverManager::getCantUseReason($code) : null;

        $params['code'] = $code;
        $params['error'] = $error;
        $params['mode'] = $code ? 'use' : 'get';

        echo $this->getFoldedEntity()->fetchTpl($params);
    }

    public function getJsParams() {
        
    }

    public function getSmartyParams4Resources() {
        
    }

}

?>
