<?php

/**
 * Форма AdminFoldingConstructorForm
 *
 * @author Admin
 */
class FORM_AdminFoldingConstructorForm extends BaseAjaxForm {

    const BUTTON_SAVE = 'Сохранить';

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_AUTHORIZED_AS_ADMIN;
    }

    protected function _construct() {
        parent::_construct();
        $this->setSmartyParam('ifaces', AdminFoldedManager::inst()->getFoldedInterfaces());
        $this->setSmartyParam('rtypes', PsUtil::getClassConsts('FoldedResources', 'RTYPE_'));
    }

    protected function processImpl(PostArrayAdapter $adapter, $button) {

        AdminFoldedManager::inst()->makeNewFolding($adapter);

        return new AjaxSuccess(print_r($adapter->getData(), true));
    }

}

?>