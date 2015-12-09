<?php

class PostActivateAction extends AbstractAdminAjaxAction {

    protected function getRequiredParamKeys() {
        return array('id', 'date', 'show', 'type');
    }

    protected function executeImpl(ArrayAdapter $params) {
        $id = $params->int('id');
        $uts = $params->int('date');
        $show = $params->bool('show');
        $type = $params->str('type');

        $pp = Handlers::getInstance()->getPostsProcessorByPostType($type);

        AdminPostsBean::inst()->updateState($pp->dbBean(), $id, $uts, $show);

        return new AjaxSuccess();
    }

}

?>
