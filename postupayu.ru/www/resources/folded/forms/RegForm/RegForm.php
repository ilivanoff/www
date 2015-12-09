<?php

/**
 * Форма регистрации
 *
 */
require_once 'RegFormData.php';

class FORM_RegForm extends BaseAjaxForm implements CheckCaptureForm {

    const BUTTON_SEND = 'Отправить';

    protected function processImpl(PostArrayAdapter $adapter, $button) {
        $data = new RegFormData();

        /*
         * Имя пользователя
         */
        $name = $adapter->str(FORM_PARAM_REG_NAME);
        $error = UserInputValidator::validateShortText($name);
        if ($error) {
            return array(FORM_PARAM_REG_NAME => $error);
        }
        $name = UserInputTools::safeShortText($name);
        $data->setUserName($name);

        /*
         * e-mail
         */
        $mail = $adapter->str(FORM_PARAM_REG_MAIL);
        $error = UserInputValidator::validateEmail($mail);
        if ($error) {
            return array(FORM_PARAM_REG_MAIL => $error);
        }
        $data->setUserMail($mail);


        /*
         * Пол
         */
        $sex = $adapter->int(FORM_PARAM_REG_SEX);
        $error = UserInputValidator::validateSex($sex);
        if ($error) {
            return array(FORM_PARAM_REG_SEX => $error);
        }
        $data->setSex($sex);

        /*
         * Пароль
         */
        $pass = $adapter->str(FORM_PARAM_REG_PASS);
        $passConfirm = $adapter->str(FORM_PARAM_REG_PASS_CONF);
        $error = UserInputValidator::validatePass($pass, $passConfirm);
        if ($error) {
            return array(FORM_PARAM_REG_PASS => $error);
        }
        $error = UserInputValidator::validatePassConfirm($pass, $passConfirm);
        if ($error) {
            return array(FORM_PARAM_REG_PASS_CONF => $error);
        }

        $data->setPassword($pass);

        AuthManager::createUser($data);

        return new AjaxSuccess();
    }

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_NOT_AUTHORIZED;
    }

}

?>