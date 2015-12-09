<?php

class PP_404 extends BasePopupPage {

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    public function doProcess(ArrayAdapter $params) {
        
    }

    public function getTitle() {
        return 'Полный список страниц';
    }

    public function getDescr() {
        return 'Полный список страниц';
    }

    public function getJsParams() {
        
    }

    public function buildContent() {
        $pages = PopupPagesManager::inst()->getPagesList();
        return $this->getFoldedEntity()->fetchTpl(array('pages' => $pages));
    }

    public function getSmartyParams4Resources() {
        
    }

}

?>
