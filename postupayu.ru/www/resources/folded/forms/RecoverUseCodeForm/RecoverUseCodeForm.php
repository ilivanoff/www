<?php

/**
 * Форма использования кода восстановления пароля
 *
 */
class FORM_RecoverUseCodeForm extends BaseAjaxForm {

    const BUTTON_SAVE = 'Сохранить';

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_NOT_AUTHORIZED;
    }

    protected function processImpl(PostArrayAdapter $adapter, $button) {
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

        /*
         * Код
         */
        $code = $adapter->str(REMIND_CODE_PARAM);
        if (!$code) {
            return 'Не передан код восстановления';
        }

        $user = PassRecoverManager::changePassWithCode($code, $pass);
        if ($user instanceof PsUser) {
            $authed = AuthManager::loginUser($user->getEmail(), $pass);
            if ($authed) {
                return new AjaxSuccess();
            } else {
                return 'Не удалось авторизоваться после смены пароля';
            }
        } else {
            //Описание - почему код не может быть использован
            return $user;
        }
    }

}

?>