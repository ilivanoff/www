<?php

/**
 * Форма pattern
 *
 * @author Admin
 */
class FORM_pattern extends BaseAjaxForm implements CheckActivityForm, CheckCaptureForm {

    const BUTTON_SAVE = 'Сохранить';

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    protected function processImpl(PostArrayAdapter $adapter, $button) {
        $text = $adapter->str('text');
        if (!$text) {
            return array('text', 'required');
        }

        return new AjaxSuccess();
    }

}

?>
