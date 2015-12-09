<?php

require_once 'AdminLoginFormData.php';

class FORM_AdminLoginForm extends AbstractForm {

    protected $CAN_RESET = false;

    const BUTTON_LOGIN = 'Войти';

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    public function processImpl(PostArrayAdapter $paa, $button) {
        $login = $paa->str(FORM_PARAM_LOGIN);
        if (!$login) {
            return 'Укажите e-mail';
        }
        if (!PsCheck::isEmail($login)) {
            return 'E-mail должен быть корректным';
        }

        $password = $paa->str(FORM_PARAM_PASS);
        if (!$password) {
            return 'Нужно указать пароль';
        }

        return new AdminLoginFormData($login, $password);
    }

}

?>