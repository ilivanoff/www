<?php

/**
 * Класс, отвечающий за авторизацию и регистрацию пользователей в системе
 */
final class AuthManager {

    const AUTH_TYPE_NO_MATTER = 'NO_MATTER';
    const AUTH_TYPE_AUTHORIZED = 'AUTHORIZED';
    const AUTH_TYPE_NOT_AUTHORIZED = 'NOT_AUTHORIZED';
    const AUTH_TYPE_AUTHORIZED_AS_ADMIN = 'AUTHORIZED_AS_ADMIN';

    /*
     * ===================================
     * ПРОВЕРКА ДОСТУПА И ТИПА АВТОРИЗАЦИИ
     * ===================================
     */

    /**
     * Основной метод проверки доступа пользователя
     * 
     * @param str $authType - тип авторизации
     * @param bool $assert  - признак, ругаться ли при отсутствии доступа
     * @return boolean      - признак авторизованности
     */
    private static final function checkAccessImpl($authType, $assert) {
        $authorized = SessionArrayHelper::hasInt(SESSION_USER_PARAM);

        switch ($authType) {
            case self::AUTH_TYPE_NO_MATTER:
                return true;

            case self::AUTH_TYPE_AUTHORIZED:
                check_condition(!$assert || $authorized, 'Пользователь не авторизован');
                return $authorized;

            case self::AUTH_TYPE_NOT_AUTHORIZED:
                check_condition(!$assert || !$authorized, 'Пользователь авторизован');
                return !$authorized;

            case self::AUTH_TYPE_AUTHORIZED_AS_ADMIN:
                $authorizedAsAdmin = $authorized && UserBean::inst()->isAdmin(SessionArrayHelper::getInt(SESSION_USER_PARAM));
                check_condition(!$assert || $authorizedAsAdmin, 'Ошибка доступа');
                return $authorizedAsAdmin;
        }

        raise_error("Неизвестный тип авторизации: [$authType]");
    }

    /**
     * Метод проверяет, имеет ли авторизованный пользователь заданный уровень доступа
     */
    public static function hasAccess($authType) {
        return self::checkAccessImpl($authType, false);
    }

    /**
     * Метод проверяет, имеет ли авторизованный пользователь заданный уровень доступа и,
     * если не имеет, выбрасывает соответствующее исключение.
     */
    public static function checkAccess($authType) {
        return self::checkAccessImpl($authType, true);
    }

    /**
     * Метод проверяет, авторизован ли пользователь
     */
    public static function isAuthorized() {
        return self::hasAccess(self::AUTH_TYPE_AUTHORIZED);
    }

    /**
     * Метод проверяет, авторизован ли пользователь как администратор
     */
    public static function isAuthorizedAsAdmin() {
        return self::hasAccess(self::AUTH_TYPE_AUTHORIZED_AS_ADMIN);
    }

    /**
     * Метод проверяет, что пользователь авторизован
     */
    public static function checkAuthorized() {
        return self::checkAccess(self::AUTH_TYPE_AUTHORIZED);
    }

    /**
     * Метод проверяет, что пользователь имеет права доступа админа
     */
    public static function checkAdminAccess() {
        return self::checkAccess(self::AUTH_TYPE_AUTHORIZED_AS_ADMIN);
    }

    /*
     * ===========================================
     * РАБОТА С КОДОМ АВТОРИЗОВАННОГО ПОЛЬЗОВАТЕЛЯ
     * ===========================================
     */

    /**
     * Возвращает код текущего авторизованного пользователя.
     */
    public static function getUserId($nullAllowed = false) {
        return $nullAllowed || self::checkAuthorized() ? SessionArrayHelper::getInt(SESSION_USER_PARAM) : null;
    }

    /**
     * Возвращает код текущего авторизованного пользователя.
     * Если пользователь не авторизован, вернётся null.
     */
    public static function getUserIdOrNull() {
        return self::getUserId(true);
    }

    /**
     * Метод валидирует код пользователя
     * 
     * @param int  $userId      - код пользователя для проверки
     * @param bool $nullAllowed - признак, допускается ли пустой код пользователя
     */
    public static function validateUserId($userId, $nullAllowed = false) {
        return is_inumeric($userId) ? (int) $userId : ($nullAllowed && !$userId ? null : raise_error("Не целочисленный код пользователя [$userId]"));
    }

    /**
     * Метод валидирует код пользователя. Допускается null.
     * @param int  $userId      - код пользователя для проверки
     */
    public static function validateUserIdOrNull($userId) {
        return self::validateUserId($userId, true);
    }

    /**
     * Метод валидирует код пользователя и если он валиден, возвращает его, иначе берёт код пользователя из сессии.
     * Пользователь обязательно должен быть!
     * 
     * @param int  $userId      - возможный код пользователя. Может быть либо числом, либо null`ом. Иначе - ошибка.
     */
    public static function extractUserId($userId, $nullAllowed = false) {
        $userId = self::validateUserIdOrNull($userId);
        return is_integer($userId) ? $userId : self::getUserId($nullAllowed);
    }

    /**
     * Возвращает код пользователя для указанного типа вторизации.
     */
    public static function extractUserId4AuthType($userId, $accessType) {
        return self::extractUserId($userId, in_array($accessType, array(self::AUTH_TYPE_NO_MATTER, self::AUTH_TYPE_NOT_AUTHORIZED)));
    }

    /**
     * Проверка, является ли переданнй пользователь или код пользователя - текущим
     * авторизованным пользователем
     */
    public static function isIt($UserOrId) {
        if (!$UserOrId || !self::isAuthorized()) {
            return false; //---
        }

        if (is_inumeric($UserOrId)) {
            return self::getUserId() === 1 * $UserOrId;
        }

        if ($UserOrId instanceof PsUser) {
            return self::getUserId() === $UserOrId->getId();
        }

        return false;
    }

    /*
     * ==================================
     * СОЗДАНИЕ/ЛОГИН/ЛОГАУТ ПОЛЬЗОВАЛЕТЯ
     * ==================================
     */

    /**
     * Основной метод авторизации пользователей в системе
     */
    private static final function loginImpl($login, $passwd, UserLoadType $userType, $afterRegistration = false) {
        self::logout();

        $userId = UserBean::inst()->getUserIdByMailPass($login, $passwd, $userType);

        if (is_integer($userId)) {
            /*
             * Пользователь авторизован!
             */
            SessionArrayHelper::setInt(SESSION_USER_PARAM, $userId);
        }

        if (self::isAuthorized()) {
            //Убедимся в наличии пользователя
            $user = PsUser::inst($userId, true);

            try {
                if ($afterRegistration) {
                    ApplicationListener::afterUserRegistered($user);
                }
                //Оповещаем слушатель об успешной авторизации пользователя.
                ApplicationListener::afterLogin($user);
            } catch (Exception $ex) {
                //Сделаем дамп ошибки
                ExceptionHandler::dumpError($ex);
            }
        } else {
            check_condition(!$afterRegistration, 'Не удалось авторизоваться после создания пользователя');
        }

        return self::isAuthorized();
    }

    /**
     * Создание нового пользователя на основе данных формы регистрации
     * @return type
     */
    public static function createUser(RegFormData $regData) {
        //Создадим пользователя в базе
        UserBean::inst()->createUser($regData);
        //Авторизуем нового пользователя
        self::loginImpl($regData->getUserMail(), $regData->getPassword(), UserLoadType::CLIENT(), true);
    }

    /**
     * Основной метод авторизации пользователей в системе
     */
    public static function loginUser($login, $passwd) {
        return self::loginImpl($login, $passwd, UserLoadType::CLIENT());
    }

    /**
     * Основной метод авторизации администраторов в системе
     */
    public static function loginAdmin($login, $passwd) {
        return self::loginImpl($login, $passwd, UserLoadType::ADMIN());
    }

    /**
     * Метод выполняет логаут пользователя
     */
    public static function logout() {
        if (self::isAuthorized()) {
            //Оповещаем слушатель о разлогинивании пользователя.
            ApplicationListener::beforeLogout(PsUser::inst());

            //Сбросим код пользователя в сессии
            SessionArrayHelper::reset(SESSION_USER_PARAM);
        }
    }

    /*
     * =======================
     * МАРКЕР СЕССИИ ДЛЯ FLASH
     * =======================
     */

    private static $SESSION_MARKER_SALT = 'c4ca4238a0b923820dcc509a6f75849b6a9sg6jvb4dj1i94i1u1fltga4';

    public static function getUserSessoinMarker() {
        return self::isAuthorized() ? md5(self::$SESSION_MARKER_SALT . self::getUserId()) . session_id() : null;
    }

    public static function checkUserSessionMarker($marker) {
        return self::checkAuthorized() && (self::getUserSessoinMarker() === $marker);
    }

}

?>