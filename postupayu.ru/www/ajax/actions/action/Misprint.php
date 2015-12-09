<?php

class Misprint extends AbstractAjaxAction {

    protected function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    protected function getRequiredParamKeys() {
        return array('url', 'text', 'note');
    }

    protected function isCheckActivity() {
        return true;
    }

    protected function executeImpl(ArrayAdapter $params) {
        $url = $params->str('url');
        $text = $params->str('text');
        $note = $params->str('note');

        if (!$url || !$text) {
            return 'Не передан url или текст';
        }

        /*
         * Если пользователь не просматривал эту страницу и это не администратор - игнорируем.
         */
        $wasOpened = PageOpenWatcher::isPageOpenedByUser($url, PsUser::instOrNull());
        if (!$wasOpened && !AuthManager::isAuthorizedAsAdmin()) {
            return 'Пользователь не открывал страницу'; //---
        }

        $text = UserInputTools::safeShortText($text);
        $note = $note ? UserInputTools::safeLongText($note) : $note;

        $saved = UtilsBean::inst()->saveMisprint($url, $text, $note, AuthManager::getUserIdOrNull());
        if (!$saved) {
            return 'Запись не была сохранена'; //---
        }

        return new AjaxSuccess();
    }

}

?>
