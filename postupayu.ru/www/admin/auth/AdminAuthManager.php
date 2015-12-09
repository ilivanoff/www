<?php

class AdminAuthManager {
    /*
     * АВТОРИЗАЦИЯ ПОЛЬЗОВАТЕЛЯ
     */

    public function login() {
        if (FORM_AdminLoginForm::getInstance()->isValid4Process()) {
            $data = FORM_AdminLoginForm::getInstance()->getData();

            $login = $data->getLogin();
            $passwd = $data->getPassword();

            AuthManager::loginAdmin($login, $passwd);
        }
        return AuthManager::isAuthorized();
    }

    /*
     * СИНГЛТОН
     */

    private static $instance = NULL;

    /** @return AdminAuthManager */
    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new AdminAuthManager();
        }
        return self::$instance;
    }

}

?>