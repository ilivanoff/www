<?php

class FeedbackAction extends AbstractAdminAjaxAction {

    protected function getRequiredParamKeys() {
        return array('id', 'action');
    }

    protected function executeImpl(ArrayAdapter $params) {
        $id = $params->int('id'); //feedId or userId - в зависимости от action
        $action = $params->str('action');

        $res = 'OK';

        switch ($action) {
            case 'delete':
                AdminFeedbackBean::inst()->deleteAnonimMsg($id);
                break;
            case 'load':
                //id = userId
                $res = FeedbackManager::inst()->buildDiscussion(false, $id, false);
                break;
            default:
                raise_error("Unknown action: $action");
        }

        return new AjaxSuccess($res);
    }

}

?>
