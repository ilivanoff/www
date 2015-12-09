<?php

abstract class BaseAdminPage extends FoldedClass {

    protected $LOGGER;

    protected function _construct() {
        $this->LOGGER = PsLogger::inst(get_called_class());
    }

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_AUTHORIZED_AS_ADMIN;
    }

    /** @return BaseAdminPage */
    public static function getInstance() {
        return parent::inst();
    }

    public abstract function title()

    ;

    public abstract function buildContent()

    ;

    public function getSmartyParams4Resources() {
        return array();
    }

    public function getJsParams() {
        return array();
    }

    public static function getPageIdent() {
        return self::getIdent();
    }

    public static function pageUrl($params = null) {
        return AdminPagesManager::getInstance()->pageUrl(self::getPageIdent(), $params);
    }

}

?>