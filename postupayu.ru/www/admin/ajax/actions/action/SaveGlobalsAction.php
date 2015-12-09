<?php

class SaveGlobalsAction extends AbstractAdminAjaxAction {

    protected function getRequiredParamKeys() {
        return array('globals');
    }

    protected function executeImpl(ArrayAdapter $params) {
        PsGlobals::inst()->updateProps($params->arr('globals'));
        return new AjaxSuccess();
    }

}

?>