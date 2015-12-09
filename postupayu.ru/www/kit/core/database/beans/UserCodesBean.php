<?php

/**
 * Бин для работы с кодами, высылаемыми пользователям.
 * Задача проста - следить, что в каждый момент времени существует 0 или 1 активный код.
 * При этом нам всегда очень важно иметь самую актуальную информацию о коде, поэтому не будем её кешировать,
 * а всегда юудем запрашивать по типу и коду, у нас есть уникальный индекс на эти поля.
 */
class UserCodesBean extends BaseBean {

    const CODE_STATUS_NOT_EXISTS = 0;
    const CODE_STATUS_INVALID = 1;
    const CODE_STATUS_ACTIVE = 2;
    const CODE_STATUS_USED = 3;

    //
    const CODE_LENGTH = 32;

    /**
     * Метод удаляет неиспользованные коды, высланные пользователю
     */
    public function dropUnusedCodes($type, $userId) {
        $this->update('delete from ps_user_codes where v_type=? and id_user=? and n_status!=?', array($type, AuthManager::validateUserId($userId), self::CODE_STATUS_USED));
    }

    /**
     * Проверяет код на соответствие формату
     */
    private function isValid($code) {
        return !!$code && ps_is_lower($code) && (self::CODE_LENGTH == strlen($code));
    }

    /**
     * Метод проверяет переданный нам код. Проверка делится на два этапа:
     * 
     * 1. Проверка кода на непустоту
     * Данная проверка должна обязательно выполняться, так как мы ещё и защищаемся от вызова методов 
     * для экземпляра кода из контекста контроллера.
     * 
     * 2. Проверка валидности формата кода
     * Вопрос - как нам поступать, если мы в один день изменим механизм генерации кодов?
     * Возможно следует оставить только проверку 1, но пока оставим и эту.
     */
    private function checkCode($code) {
        check_condition($code, 'Пустой код');
        check_condition($this->isValid($code), "Невалидный код [$code]");
        return $code;
    }

    /**
     * Метод генерирует новый код и вставляет его в базу
     */
    public function generateAndSave($type, $userId) {
        $this->dropUnusedCodes($type, $userId);
        $code = $this->checkCode(PsRand::string(self::CODE_LENGTH, false, true));
        $affected = $this->update('insert into ps_user_codes (v_type, id_user, v_code, dt_add, n_status) values (?, ?, ?, unix_timestamp(), ?)', array($type, $userId, $code, self::CODE_STATUS_ACTIVE));
        check_condition($affected == 1, "Код не был сохранён в базу");
        return $code;
    }

    /**
     * Метод получает id_user польователя, которому был выслан данный код
     * @return int | null  Если код не существует, то будет возвращён null
     */
    public function getUserId($type, $code) {
        return $this->getInt('select id_user from ps_user_codes where v_type=? and v_code=?', array($type, $this->checkCode($code)));
    }

    /**
     * Метод получает n_status переданного кода. Если код не существует, то будет возвращёт статус CODE_STATUS_NOT_EXISTS
     */
    public function getCodeState($type, $code) {
        return $this->isValid($code) ? $this->getInt('select n_status from ps_user_codes where v_type=? and v_code=?', array($type, $this->checkCode($code)), self::CODE_STATUS_NOT_EXISTS) : self::CODE_STATUS_INVALID;
    }

    /**
     * Метод отмечает код, как использованный.
     * Если не удастся это сделать, то будет выброшена ошибка.
     */
    public function markCodeAsUsed($type, $code) {
        $affected = $this->update('update ps_user_codes SET dt_used=UNIX_TIMESTAMP(), n_status=? where v_type=? and v_code=? and n_status=?', array(self::CODE_STATUS_USED, $type, $this->checkCode($code), self::CODE_STATUS_ACTIVE));
        check_condition($affected == 1, 'Код не был отмечен, как использованный');
        $this->dropUnusedCodes($type, $this->getUserId($type, $code));
    }

    /** @return UserCodesBean */
    public static function inst() {
        return parent::inst();
    }

    protected function __construct() {
        //Убедимся в том, что константы класса не повторяются. Вообще более уместно использовать здесь enum.
        PsUtil::assertClassHasDifferentConstValues(__CLASS__, 'CODE_STATUS_');
        parent::__construct();
    }

}

?>