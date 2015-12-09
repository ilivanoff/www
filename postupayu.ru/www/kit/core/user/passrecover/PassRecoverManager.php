<?php

/**
 * Класс содержит всё необходимое для восстановления пароля пользователя.
 */
class PassRecoverManager {

    /**
     * Метод генерирует и высылает код восстановления.
     */
    public static function sendRecoverCode($email) {
        EMAIL_pwdremind::inst()->send($email);
    }

    /**
     * Метод использует ранее отправленный пользователю код для смены пароля.
     * @return PsUser - пользователь, для которого был изменён пароль
     */
    public static function changePassWithCode($code, $newPlainPass) {
        return PsUserCode::passRecoverCode($code)->markAsUsed()->getUser()->updatePassword($newPlainPass, false);
    }

    /**
     * Метод возвращает причину, по которой код не может быть использован.
     * Если код может быть использован, вернётся null.
     */
    public static function getCantUseReason($code) {
        return PsUserCode::passRecoverCode($code)->getCantUseReason();
    }

}

?>