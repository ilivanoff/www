<?php

class ActivityWatcher {

    /**
     * Регистрация активности пользователя
     */
    public static function registerActivity() {
        $_SESSION[SESSION_ACT_WATCHER_PARAM] = time();
    }

    /**
     * Метод проверяет, была ли зарегистрирована активность данного пользователя
     */
    private static function isActivityRegistered() {
        return is_numeric(array_get_value(SESSION_ACT_WATCHER_PARAM, $_SESSION));
    }

    /**
     * Метод возвращает признак - может ли пользователь выполнить действие
     */
    public static function isCanMakeAction() {
        return self::getWaitTime() <= 0;
    }

    /**
     * Возвращает количество секунд, которое нужно подождать перед тем, 
     * как можно будет выполнить очередное дейтсие.
     */
    public static function getWaitTime() {
        $needWait = 0;
        //Не будем заставлять админа ждать:)
        if (self::isActivityRegistered()) {
            $needWait = PsSettings::ACTIVITY_INTERVAL() - (time() - $_SESSION[SESSION_ACT_WATCHER_PARAM]);
        }
        return $needWait < 0 ? 0 : $needWait;
    }

}

?>