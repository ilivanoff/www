<?php

class UserAvatarsAction extends AbstractAjaxAction {

    protected function getAuthType() {
        return AuthManager::AUTH_TYPE_AUTHORIZED;
    }

    protected function isCheckActivity() {
        return false;
    }

    protected function getRequiredParamKeys() {
        return array('id', 'action');
    }

    protected function executeImpl(ArrayAdapter $params) {
        $avatarId = $params->int('id'); //Может быть передан и PsConstJs::AVATAR_NO_SUFFIX, а значит - null
        $action = $params->str('action');

        $USER = PsUser::inst();

        switch ($action) {
            case 'set':
                check_condition($USER->setAvatar($avatarId), 'Ошибка установки текущего аватара.');
                break;
            case 'del':
                check_condition($USER->deteleAvatar($avatarId), 'Ошибка удаления аватара.');
                break;
            default:
                raise_error("Неизвестное действие [$action].");
        }

        $result['id'] = $USER->hasAvatar() ? $USER->getAvatarId() : PsConstJs::AVATAR_NO_SUFFIX;
        $result['src_big'] = $USER->getAvatarRelPath(PsUser::ID_CARD_AVATAR_DIM);
        return new AjaxSuccess($result);
    }

}

?>