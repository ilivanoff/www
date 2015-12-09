<?php

/**
 * Слушатель событий приложения.
 */
final class ApplicationListener {

    public static function afterUserRegistered(PsUser $user) {
        //Отправим приветственное сообщение
        MSG_UserRegistered::inst()->sendSystemMsg($user);
        //Дадим очки за регистрацию
        UP_registration::inst()->givePoints($user);
        //Привяжем ему набор дефолтных страниц - плагинов
        PopupPagesManager::inst()->bindDefaultPages2User($user->getId());
    }

    public static function afterLogin(PsUser $user) {
        //Проверим, а нет ли у пользователя очков, которые он заслужил
        UserPointsManager::inst()->checkAllUserPoints($user);
        //Аудит
        UserAudit::inst()->afterLogin($user->getId());
    }

    public static function beforeLogout(PsUser $user) {
        //Аудит
        UserAudit::inst()->beforeLogout($user->getId());
    }

}

?>