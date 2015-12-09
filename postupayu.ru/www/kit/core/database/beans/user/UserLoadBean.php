<?php

/**
 * Класс инкапсулирует методы для загрузки данных о пользователе
 *
 * @author azazello
 */
abstract class UserLoadBean extends UserBatchLoadBean {

    const FIELD_ID = 'id_user';
    const FIELD_NAME = 'user_name';
    const FIELD_DT_REG = 'dt_reg';
    const FIELD_SEX = 'b_sex';
    const FIELD_EMAIL = 'email';
    const FIELD_PASSWD = 'passwd';
    const FIELD_B_ADMIN = 'b_admin';
    const FIELD_B_CAN_LOGIN = 'b_can_login';
    const FIELD_ID_AVATAR = 'id_avatar';
    const FIELD_TIMEZONE = 'timezone';
    const FIELD_ABOUT = 'about';
    const FIELD_ABOUT_SRC = 'about_src';
    const FIELD_CONTACTS = 'contacts';
    const FIELD_CONTACTS_SRC = 'contacts_src';
    const FIELD_MSG = 'msg';
    const FIELD_MSG_SRC = 'msg_src';

    public static function getColumns() {
        return PsUtil::getClassConsts(__CLASS__, 'FIELD_');
    }

    /**
     * Метод возвращает признак, имеется ли в таблице users заданное поле
     */
    public static function hasColumn($colName) {
        return in_array($colName, self::getColumns());
    }

    /**
     * Функция хеширования пароля пользователя
     */
    public static final function hashPassword($password) {
        return md5($password);
    }

    /**
     * Основной метод получения кода пользователя, который не забудет его провалидировать и зарегистрировать в батче
     */
    private function getUserId(array $whereAssoc, UserLoadType $loadType) {
        return $this->register($this->getInt(Query::select(self::FIELD_ID, 'users', array($whereAssoc, $loadType->getRestriction()))), true);
    }

    /**
     * Главный метод авторизации, используется для получения пользователя по логину и паролю.
     */
    public final function getUserIdByMailPass($login, $passwd, UserLoadType $loadType) {
        return $this->getUserId(array(self::FIELD_EMAIL => $login, self::FIELD_PASSWD => self::hashPassword($passwd)), $loadType);
    }

    /**
     * Для восстановления пароля
     */
    public final function getUserIdByMail($email) {
        return $this->getUserId(array(self::FIELD_EMAIL => $email), UserLoadType::CLIENT());
    }

    /*
     * ************
     *   ПРОВЕРКА
     * ************
     */

    /**
     * Метод проверяет, зарегистрирован ли такой email в базе
     */
    public final function hasMail($email) {
        return $this->hasRec('users', array(self::FIELD_EMAIL => PsCheck::email($email)));
    }

    /**
     * Метод проверяет, является ли пользователь - администратором.
     */
    public final function isAdmin($userId) {
        return 1 === (int) $this->getUserProperty($userId, self::FIELD_B_ADMIN);
    }

}

?>