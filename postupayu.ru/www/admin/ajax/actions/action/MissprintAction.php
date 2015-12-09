<?php

class MissprintAction extends AbstractAdminAjaxAction {

    protected function getRequiredParamKeys() {
        return array('id');
    }

    protected function executeImpl(ArrayAdapter $params) {
        UtilsBean::inst()->removeMissprint($params->int('id'));
        return new AjaxSuccess();
    }

}

?>
