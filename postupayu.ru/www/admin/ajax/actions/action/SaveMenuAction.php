<?php

class SaveMenuAction extends AbstractAdminAjaxAction {

    protected function getRequiredParamKeys() {
        return array('menu');
    }

    protected function executeImpl(ArrayAdapter $params) {
        $APM = AdminPagesManager::getInstance();
        $APM->saveLayout($params->arr('menu'));
        return new AjaxSuccess($APM->getLayout());
    }

}

?>