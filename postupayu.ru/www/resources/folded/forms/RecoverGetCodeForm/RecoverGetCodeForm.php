<?php

/**
 * Форма получения кода восстановления пароля
 *
 */
class FORM_RecoverGetCodeForm extends BaseAjaxForm implements CheckCaptureForm {

    const BUTTON_SEND = 'Отправить';

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_NOT_AUTHORIZED;
    }

    protected function processImpl(PostArrayAdapter $adapter, $button) {
        /*
         * e-mail
         */
        $mail = $adapter->str(FORM_PARAM_REG_MAIL);
        $error = UserInputValidator::validateEmail($mail, true);
        if ($error) {
            return array(FORM_PARAM_REG_MAIL => $error);
        }

        PassRecoverManager::sendRecoverCode($mail);
        return new AjaxSuccess();
    }

}

?>