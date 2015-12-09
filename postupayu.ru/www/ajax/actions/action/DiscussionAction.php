<?php

class DiscussionAction extends AbstractAjaxAction {

    const JS_PARAM_ACTION = 'action';
    const JS_PARAM_MSG_ID = 'msgId';
    const JS_PARAM_ROOT_ID = 'rootId';

    protected function getAuthType() {
        //TODO AuthManager::AUTH_TYPE_AUTHORIZED
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    protected function isCheckActivity() {
        return false;
    }

    protected function getRequiredParamKeys() {
        return array_merge(DiscussionController::getJsDataKeys(), PsUtil::getClassConsts(__CLASS__, 'JS_PARAM_ACTION'));
    }

    protected function executeImpl(ArrayAdapter $params) {
        $unique = $params->str(DiscussionController::JS_DATA_UNIQUE);
        $upDown = $params->bool(DiscussionController::JS_DATA_UPDOWN);
        $entity = $params->int(DiscussionController::JS_DATA_THREAD);

        $action = $params->str(self::JS_PARAM_ACTION);
        $msgId = $params->int(self::JS_PARAM_MSG_ID);
        $rootId = $params->int(self::JS_PARAM_ROOT_ID);

        $controller = Handlers::getInstance()->getDiscussionController($unique);

        if (in_array($action, DiscussionController::getCommentActions())) {
            return new AjaxSuccess($controller->executeCommentAction($msgId, $action));
        }

        if ($action == DiscussionController::TREE_ACTION_LOAD_COMMENTS) {
            return new AjaxSuccess($controller->loadTree($rootId, $upDown, $entity));
        }

        return "Неизвестное действие: [$action]";
    }

}

?>