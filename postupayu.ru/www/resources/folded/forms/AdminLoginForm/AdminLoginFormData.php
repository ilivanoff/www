<?php

/**
 * Форма авторизации
 *
 * @author Admin
 */
class AdminLoginFormData implements FormSuccess {

    private $login;
    private $password;

    function __construct($login, $password) {
        $this->login = $login;
        $this->password = $password;
    }

    public function getLogin() {
        return $this->login;
    }

    public function getPassword() {
        return $this->password;
    }

}

?>
