<?php

/**
 * Класс для аудита авторизаций пользователя
 *
 * @author azazello
 */
final class UserAudit extends BaseAudit {
    /**
     * Действия
     */

    const ACTION_REGISTER = 1;
    const ACTION_LOGIN = 2;
    const ACTION_LOGOUT = 3;
    const ACTION_UPDATE = 4;

    public function getProcessCode() {
        return self::CODE_USERS;
    }

    public function getDescription() {
        return 'Действия пользователя';
    }

    /**
     * Аудит регистрации пользователя
     */
    public function afterRegistered($userId, array $params) {
        $this->doAudit(self::ACTION_REGISTER, $userId, $params, true);
    }

    /**
     * Аудит входа пользователя в систему
     */
    public function afterLogin($userId) {
        $data['ip'] = ServerArrayAdapter::REMOTE_ADDR();
        $data['agent'] = ServerArrayAdapter::HTTP_USER_AGENT();
        $this->doAudit(self::ACTION_LOGIN, $userId, $data, true, self::ACTION_REGISTER);
    }

    /**
     * Аудит изменения параметров пользователя
     */
    public function onUpdate($userId, array $DIFF) {
        $this->doAudit(self::ACTION_UPDATE, $userId, $DIFF, false, self::ACTION_LOGIN, true, false);
    }

    /**
     * Аудит выхода пользователя из системы
     */
    public function beforeLogout($userId) {
        $this->doAudit(self::ACTION_LOGOUT, $userId, null, false, self::ACTION_LOGIN);
    }

    /** @return UserAudit */
    public static function inst() {
        return parent::inst();
    }

}

?>