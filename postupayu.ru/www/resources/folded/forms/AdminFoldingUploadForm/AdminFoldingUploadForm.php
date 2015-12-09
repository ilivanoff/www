<?php

/**
 * Форма AdminFoldingUploadForm
 *
 * @author Admin
 */
class FORM_AdminFoldingUploadForm extends BaseAjaxForm {

    const BUTTON_SAVE = 'Загрузить';

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_AUTHORIZED_AS_ADMIN;
    }

    protected function processImpl(PostArrayAdapter $adapter, $button) {
        $clear = $adapter->bool(FORM_PARAM_YES_NO);

        $folding = Handlers::getInstance()->getFoldingByUnique($adapter->str('folding'));

        $uploadedZip = SimpleUploader::inst()->saveUploadedFile();
        try {
            $fentity = $folding->imporFromZip($uploadedZip, $clear);
        } catch (Exception $ex) {
            $uploadedZip->remove();
            throw $ex;
        }

        return new AjaxSuccess(array('url' => AP_APFoldingEdit::url($fentity)));
    }

}

?>