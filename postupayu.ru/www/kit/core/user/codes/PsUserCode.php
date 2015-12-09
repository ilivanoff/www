<?php

/**
 * Менеджер генерации кодов, отправляемых пользователям
 *
 * @author azazello
 */
class PsUserCode implements PsUserCodeController {

    private $type;
    private $code;

    /*
     * **************
     *   КОНТРОЛЛЕР
     * **************
     */

    /**
     * Метод сбрасывает все активные коды, сгенерированные для пользователя
     */
    public function dropUnusedCodes($userId) {
        UserCodesBean::inst()->dropUnusedCodes($this->type, $userId);
    }

    /**
     * Метод генерирует новый код для отправки пользователю и сохраняет его в базу
     * @return PsUserCode
     */
    public function generateAndSave($userId) {
        return self::code($this->type, UserCodesBean::inst()->generateAndSave($this->type, $userId));
    }

    /*
     * ********************
     *   ОБЁРТКА ДЛЯ КОДА
     * ********************
     */

    public function getUserId() {
        return UserCodesBean::inst()->getUserId($this->type, $this->code);
    }

    public function getState() {
        return UserCodesBean::inst()->getCodeState($this->type, $this->code);
    }

    public function getCode() {
        return $this->code;
    }

    public function isCanUse() {
        return $this->getState() === UserCodesBean::CODE_STATUS_ACTIVE;
    }

    /**
     * Метод возвращает пользователя, для которого был сгенерирован код.
     * @return PsUser
     */
    public function getUser() {
        $userId = $this->getUserId();
        return $userId ? PsUser::inst($userId) : null;
    }

    /**
     * Метод возвращает текстовое описание состояния кода
     */
    public function getStateDescr() {
        $state = $this->getState();
        switch ($state) {
            case UserCodesBean::CODE_STATUS_NOT_EXISTS:
                return 'Код не зарегестрирован';
            case UserCodesBean::CODE_STATUS_INVALID:
                return 'Код некорректен';
            case UserCodesBean::CODE_STATUS_ACTIVE:
                return 'Код активен';
            case UserCodesBean::CODE_STATUS_USED:
                return 'Код уже использован';
        }
        raise_error("Неизвестное состояние кода [{$this->code}]: $state");
    }

    /**
     * Метод возвращает причину, по которой код не может быть использован.
     * Если код может быть использован, вернётся null.
     */
    public function getCantUseReason() {
        return $this->isCanUse() ? null : $this->getStateDescr();
    }

    /**
     * Метод отмечает код, как использованный.
     * Если это по каким-либо причинам не удастся, то будет выброшена ошибка!
     * 
     * @return PsUserCode текущий код
     */
    public function markAsUsed() {
        check_condition($this->isCanUse(), $this->getStateDescr());
        UserCodesBean::inst()->markCodeAsUsed($this->type, $this->code);
        return $this;
    }

    /*
     * 
     */

    private static $insts = array();

    /**
     * Метод возвращает класс, который предоставляет функции только для управления
     * всеми кодами - сброс неиспользованных кодов, генерацию новых и т.д.
     * 
     * @return PsUserCodeController
     */
    private static function inst($type) {
        return array_key_exists($type, self::$insts) ? self::$insts[$type] : self::$insts[$type] = new PsUserCode($type, null);
    }

    /**
     * Метод возвращает полнофункциональный класс для управления конкретным кодом.
     * Также через этот класс будут доступны и методы контроллера.
     * 
     * @return PsUserCode
     */
    private static function code($type, $code) {
        $key = $type . '-' . check_condition($code, 'Пустой код');
        return array_key_exists($key, self::$insts) ? self::$insts[$key] : self::$insts[$key] = new PsUserCode($type, $code);
    }

    private function __construct($type, $code) {
        $this->type = check_condition($type, 'Пустой тип');
        $this->code = $code;
    }

    /**
     * Экземпляры контроллеров для работы с кодами
     * */
    public static function passRecover() {
        return self::inst('R');
    }

    public static function passRecoverCode($code) {
        return self::code('R', $code);
    }

}

?>