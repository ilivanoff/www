<?php

class ToggleLessonState extends AbstractAjaxAction {

    protected function getAuthType() {
        return AuthManager::AUTH_TYPE_AUTHORIZED;
    }

    protected function isCheckActivity() {
        return false;
    }

    protected function getRequiredParamKeys() {
        return array('id');
    }

    protected function executeImpl(ArrayAdapter $params) {
        $post_id = $params->int('id');
        TrainManager::inst()->toggleLessonState($post_id);
        return new AjaxSuccess();
    }

}

?>
