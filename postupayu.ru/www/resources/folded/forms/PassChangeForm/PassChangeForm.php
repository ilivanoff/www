<?php

/**
 * Форма смены пароля
 */
class FORM_PassChangeForm extends BaseAjaxForm {

    const BUTTON_SAVE = 'Сохранить';

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_AUTHORIZED;
    }

    protected function processImpl(PostArrayAdapter $adapter, $button) {
        $oldPass = $adapter->str(FORM_PARAM_REG_OLD_PASS);
        $newPass = $adapter->str(FORM_PARAM_REG_PASS);
        $newPassConfirm = $adapter->str(FORM_PARAM_REG_PASS_CONF);

        $error = UserInputValidator::validateOldPass($oldPass);
        if ($error) {
            return array(FORM_PARAM_REG_OLD_PASS => $error);
        }
        $error = UserInputValidator::validatePass($newPass, $newPassConfirm);
        if ($error) {
            return array(FORM_PARAM_REG_PASS => $error);
        }
        $error = UserInputValidator::validatePassConfirm($newPass, $newPassConfirm);
        if ($error) {
            return array(FORM_PARAM_REG_PASS_CONF => $error);
        }

        PsUser::inst()->changePassword($oldPass, $newPass);

        return new AjaxSuccess();
    }

}

?>