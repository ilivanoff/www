<?php

class ProfilersAction extends AbstractAdminAjaxAction {

    protected function getRequiredParamKeys() {
        return array('action');
    }

    protected function executeImpl(ArrayAdapter $params) {
        $action = $params->str('action');

        $controller = PsProfiler::controller();

        switch ($action) {
            case 'reset':
                $controller->resetAll();
                break;
            case 'on':
                $controller->setProfilingEnabled(true);
                break;
            case 'off':
                $controller->setProfilingEnabled(false);
                break;
            default:
                json_error("Unknown action [$action].");
        }

        return new AjaxSuccess();
    }

}

?>
