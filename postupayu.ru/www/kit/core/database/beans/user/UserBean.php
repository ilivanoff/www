<?php

/**
 * Бил для выполнения основных операций над клиентом
 *
 * @author Admin
 */
class UserBean extends UserUpdateBean {

    /**
     * Поля, сохраняемые в аудит при апдейте записи
     */
    private static $SKIP_AUDIT_ON_CREATE_FIELDS = array(
        self::FIELD_ABOUT, self::FIELD_ABOUT_SRC,
        self::FIELD_CONTACTS, self::FIELD_CONTACTS_SRC,
        self::FIELD_MSG, self::FIELD_MSG_SRC
    );

    /**
     * Создание пользователя
     * @return int userId - код нового пользователя
     */
    public final function createUser(RegFormData $data) {
        $email = PsCheck::email($data->getUserMail());

        //Проверим, что пользователь с таким email ещё не заведён
        check_condition(!$this->hasMail($email), "Пользователь с почтой [$email] уже зарегистрирован");

        //Подготовим поля для вставки
        $params[self::FIELD_NAME] = $data->getUserName();
        $params[self::FIELD_SEX] = $data->getSex();
        $params[self::FIELD_EMAIL] = $email;
        $params[self::FIELD_PASSWD] = self::hashPassword($data->getPassword());
        $params[self::FIELD_B_ADMIN] = 0;
        $params[self::FIELD_B_CAN_LOGIN] = 1;
        $params[] = Query::assocParam(self::FIELD_DT_REG, 'UNIX_TIMESTAMP()', false);

        //Выполняем вставку
        $userId = $this->register($this->insert(Query::insert('users', $params)));

        //Сохраним данные пользователя в аудит
        UserAudit::inst()->afterRegistered($userId, array_filter_keys($this->getUserDataById($userId), self::$SKIP_AUDIT_ON_CREATE_FIELDS));

        //Возвращаем код пользователя
        return $userId;
    }

    /** @return UserBean */
    public static function inst() {
        return parent::inst();
    }

}

?>