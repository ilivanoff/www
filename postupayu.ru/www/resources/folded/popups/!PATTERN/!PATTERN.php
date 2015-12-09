<?php

class eclassname extends BasePopupPage {

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    public function doProcess(ArrayAdapter $params) {
        
    }

    public function getTitle() {
        return 'Всплывающая страница';
    }

    public function getDescr() {
        return 'Описание всплывающей страницы';
    }

    public function getJsParams() {
        
    }

    public function buildContent() {
        return $this->getFoldedEntity()->fetchTpl();
    }

    public function getSmartyParams4Resources() {
        
    }

}

?>