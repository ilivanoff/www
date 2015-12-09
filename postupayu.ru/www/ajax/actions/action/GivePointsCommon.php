<?php

/**
 * Основное ajax действие, проверяющее, можно ли дать очки пользователю.
 * Пользователь должен быть авторизован.
 */
class GivePointsCommon extends AbstractAjaxAction {

    protected function getAuthType() {
        return AuthManager::AUTH_TYPE_AUTHORIZED;
    }

    protected function isCheckActivity() {
        return false;
    }

    protected function getRequiredParamKeys() {
        return array('fentity');
    }

    protected function executeImpl(ArrayAdapter $params) {
        return new AjaxSuccess(UserPointsManager::inst()->givePointsByRequest(PsUser::inst(), $params));
    }

}

?>