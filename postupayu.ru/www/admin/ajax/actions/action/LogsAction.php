<?php

class LogsAction extends AbstractAdminAjaxAction {

    protected function getRequiredParamKeys() {
        return array('action');
    }

    protected function executeImpl(ArrayAdapter $params) {
        $action = $params->str('action');

        $controller = PsLogger::controller();

        switch ($action) {
            case 'reset':
                $controller->clearLogs();
                break;
            case 'on':
                $controller->setLoggingEnabled(true);
                break;
            case 'off':
                $controller->setLoggingEnabled(false);
                break;
            default:
                json_error("Unknown action [$action].");
        }

        return new AjaxSuccess();
    }

}

?>
