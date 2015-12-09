<?php

/**
 * Форма авторизации
 *
 * @author Admin
 */
class FORM_LoginForm extends BaseAjaxForm {

    protected $CAN_RESET = false;

    const BUTTON_LOGIN = 'Войти';

    public function getAuthType() {
        //Даже если пользователь уже авторизован, мы не можем отклонить его заявку повторно
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    protected function processImpl(PostArrayAdapter $adapter, $button) {
        $login = $adapter->str(FORM_PARAM_LOGIN);
        if (!$login) {
            return array(FORM_PARAM_LOGIN, 'required');
        }
        if (!PsCheck::isEmail($login)) {
            return array(FORM_PARAM_LOGIN, 'email');
        }

        $password = $adapter->str(FORM_PARAM_PASS);
        if (!$password) {
            return array(FORM_PARAM_PASS, 'required');
        }

        $loggedIn = AuthManager::loginUser($login, $password);

        return $loggedIn ? new AjaxSuccess() : 'В доступе отказано';
    }

}

?>
