<?php

/**
 * Класс для работы с вычисляемыми настройками
 */
class PsSettings {

    /**
     * Признак - авторизованы мы под админом или же работаем в девелопменте.
     * Часто в этом сочитании можно дать возможность выполнять дополнительные действия.
     */
    public static function DEVMODE_OR_ADMIN() {
        return !PsDefines::isProduction() || AuthManager::isAuthorizedAsAdmin();
    }

    /**
     * Возвращает минимальный интервал между действиями.
     */
    public static function ACTIVITY_INTERVAL() {
        return AuthManager::isAuthorizedAsAdmin() ? 0 : ACTIVITY_INTERVAL;
    }

}

?>