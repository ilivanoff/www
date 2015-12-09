<?php

/**
 * Действия, выполняемые админом из клиентского интерфейса (панель "Администратор" на сайте)
 *
 * @author azazello
 */
class ADMINclient extends AbstractAjaxAction {

    protected function getAuthType() {
        return AuthManager::AUTH_TYPE_AUTHORIZED_AS_ADMIN;
    }

    protected function isCheckActivity() {
        return false;
    }

    protected function getRequiredParamKeys() {
        return 'action';
    }

    protected function executeImpl(ArrayAdapter $params) {
        $id = $params->str('id');
        $type = $params->str('type');
        $action = $params->str('action');

        switch ($action) {
            case 'ccache':
                PSCache::inst()->clean();
                break;

            case 'toggledev':
                PsGlobals::inst()->getProp('PS_PRODUCTION')->setValue(!PS_PRODUCTION);
                PsGlobals::inst()->save2file();
                break;

            default:
                return 'Unknown action';
        }

        return new AjaxSuccess();
    }

}

?>
