<?php

/**
 * Базовый класс для PsUser, в который вынесен весь stuff, чтобы не загромождать основной класс.
 * 
 * Суть проста - нам достаточно передать код пользователя, далее за свежестью информации о пользователе
 * будет следить сам класс. Сами данные из БД могут быть установлены либо при получении экземпляра PsUser,
 * либо при загрузке этим самым классом.
 */
abstract class PsUserBase {

    /** @var PsLoggerInterface */
    protected $LOGGER;

    /** Код пользователя (передаётся в конструкторе) */
    protected $userId;

    /** Признак того, что мы имеем неограниченный доступ для работы с данным пользователем */
    private $canEdit;

    /**
     * Код пользователя, передаётся в конструкторе и никогда не меняется
     */
    public function getId() {
        return $this->userId;
    }

    public function getAboutSrc() {
        return $this->about_src;
    }

    public function getContactsSrc() {
        return $this->contacts_src;
    }

    public function getMsgSrc() {
        return $this->msg_src;
    }

    public function getName() {
        return $this->user_name;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getAbout() {
        return $this->about;
    }

    public function getSex() {
        return get_sex($this->b_sex);
    }

    public function getContacts() {
        return $this->contacts;
    }

    public function getDtReg($format = DF_USER_REG_EVENT) {
        return DatesTools::inst()->uts2dateInCurTZ($this->dt_reg, $format);
    }

    public function getMsg() {
        return $this->msg;
    }

    public function getTimezone() {
        return trim($this->timezone);
    }

    public function getAvatarId() {
        return $this->id_avatar;
    }

    public function hasAvatar() {
        return is_numeric($this->id_avatar);
    }

    public function checkPassword($plainPass, $assert = false) {
        $match = UserBean::hashPassword($plainPass) === $this->passwd;
        check_condition($match || !$assert, 'Пароль не совпадает');
        return $match;
    }

    /**
     * Определение пола
     */
    public function isBoy() {
        return $this->getSex() === SEX_BOY;
    }

    public function isGirl() {
        return $this->getSex() === SEX_GIRL;
    }

    /**
     * Проверка, является ли авторизованный пользователь - администратором
     */
    public final function isAdmin() {
        return UserBean::inst()->isAdmin($this->userId);
    }

    /**
     * По переданному на вход параметру определяет, является ли пользователь - указанным.
     * Можно передать:
     * 1. Код пользователя
     * 2. Объект данного класа
     */
    public final function isIt($user) {
        if (is_inumeric($user)) {
            return $this->userId === 1 * $user;
        }
        if ($user instanceof PsUser) {
            return $this->userId === $user->getId();
        }
        return false;
    }

    /**
     * Метод проверяет, авторизован ли пользователь.
     */
    public final function isAuthorised() {
        return AuthManager::isIt($this);
    }

    /**
     * Метод проверяет, авторизован ли пользователь под администратором.
     */
    public final function isAuthorisedAsAdmin() {
        return $this->isAuthorised() && AuthManager::isAuthorizedAsAdmin();
    }

    /**
     * Конструктор может быть вызван только из PsUser
     * 
     * @param int $userId - код пользователя, обязательный параметр
     * @param array $data - параметры пользователя Могут и не быть
     */
    protected function __construct($userId) {
        $this->userId = UserBean::inst()->register($userId);
        $this->canEdit = AuthManager::isIt($userId) || AuthManager::isAuthorizedAsAdmin();
        $this->LOGGER = PsLogger::inst(get_called_class());
        $this->LOGGER->info('[{}] Instance created, can edit ? {}', $this->userId, var_export($this->canEdit, true));
    }

    /**
     * Метод утверждает, что мы можем выполнять модификацию пользователя, что возможно, если:
     * 1. Данный пользователь является нами
     * 2. Мы авторизованы под администратором
     */
    protected final function assertCanEdit($__FUNCTION__, $doAssert = true) {
        if ($doAssert) {
            $this->LOGGER->info('[{}] {}({})', $this->userId, __FUNCTION__, $__FUNCTION__);
            check_condition($this->canEdit, 'Cannot call ' . $__FUNCTION__ . ' for user ' . $this->userId);
        }
    }

    /**
     * Получение свойства по его названию.
     * Если данные пользователя не установлены или требуемое свойство не загружено - загрузем данные из базы.
     */
    final function __get($property) {
        return UserBean::inst()->getUserProperty($this->userId, $property);
    }

    /**
     * Устанавливать параметры для пользователя нельзя - они должны либо передаваться при 
     * получении экземпляра, либо загружаться из базы.
     */
    final function __set($property, $value) {
        raise_error('Cannot call ' . __CLASS__ . '::' . __FUNCTION__ . "($property, ...)");
    }

    /**
     * Клонировать пользователя нельзя
     */
    final function __clone() {
        raise_error('Cannot call ' . __CLASS__ . '::' . __FUNCTION__);
    }

}

?>