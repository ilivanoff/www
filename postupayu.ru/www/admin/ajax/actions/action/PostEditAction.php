<?php

class PostEditAction extends AbstractAdminAjaxAction {

    protected function getRequiredParamKeys() {
        return array('name', 'ident', 'type');
    }

    protected function executeImpl(ArrayAdapter $params) {
        $name = $params->str('name');
        $type = $params->str('type');
        $ident = $params->str('ident');
        $rubId = $params->int('rubId');

        check_condition($name, 'Name is empty');

        $pp = Handlers::getInstance()->getPostsProcessorByPostType($type);
        $pp->getFolding()->assertExistsEntity($ident);

        AdminPostsBean::inst()->registerPost($pp->dbBean(), $ident, $name, $rubId);

        return new AjaxSuccess();
    }

}

?>
