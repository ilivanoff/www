<?php

/**
 * Менеджер, ведущий журнал по открытию страниц от имени пользователя
 */
final class PageOpenWatcher {

    /**
     * Метод обновляет время последнего просмотра страницы
     */
    public static function updateUserPageWatch($url) {
        UtilsBean::inst()->savePageWatch(AuthManager::getUserIdOrNull(), $url);
    }

    /**
     * Метод получает время последнего просмотра страницы для текущего пользователя
     */
    public static function getUserLastPageWatch($url, PsUser $user = null) {
        return $user ? UtilsBean::inst()->getLastPageWatch($user->getId(), $url) : null;
    }

    /**
     * Метод проверяет, была ли страница просмотрена пользователем.
     * Если пользователь не авторизован, то проверяет без привязки к пользователю.
     */
    public static function isPageOpenedByUser($url, PsUser $user = null) {
        return UtilsBean::inst()->isPageWasOpened($url, $user ? $user->getId() : null);
    }

}

?>