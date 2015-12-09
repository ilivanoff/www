<?php

class DiscussionAddMsg extends AbstractAjaxAction {

    const JS_PARAM_THEME = FORM_PARAM_THEME;
    const JS_PARAM_COMMENT = FORM_PARAM_COMMENT;
    const JS_PARAM_PARENT_ID = FORM_PARAM_PARENT_ID;

    protected function getAuthType() {
        return AuthManager::AUTH_TYPE_AUTHORIZED;
    }

    protected function isCheckActivity() {
        return true;
    }

    protected function getRequiredParamKeys() {
        return array_merge(DiscussionController::getJsDataKeys(), PsUtil::getClassConsts(__CLASS__, 'JS_PARAM_'));
    }

    protected function executeImpl(ArrayAdapter $params) {
        $unique = $params->str(DiscussionController::JS_DATA_UNIQUE);
        $upDown = $params->bool(DiscussionController::JS_DATA_UPDOWN);
        $entity = $params->str(DiscussionController::JS_DATA_THREAD);

        $theme = $params->str(self::JS_PARAM_THEME);
        $comment = $params->str(self::JS_PARAM_COMMENT);
        $parentId = $params->int(self::JS_PARAM_PARENT_ID);

        $controller = Handlers::getInstance()->getDiscussionController($unique);

        //Валидируем тему
        if (!$parentId && $controller->getDiscussionSettings()->isThemed()) {
            if (!$theme) {
                return 'Введите тему';
            }
            $error = UserInputValidator::validateShortText($theme);
            if ($error) {
                return $error;
            }
            $theme = UserInputTools::safeShortText($theme);
        }

        //Валидируем комментарий
        if (!$comment) {
            return 'Введите комментарий';
        }
        $error = UserInputValidator::validateLongText($comment);
        if ($error) {
            return $error;
        }
        $comment = UserInputTools::safeLongText($comment);

        $msgObj = $controller->saveMessage($entity, $parentId, $comment, $theme, PsUser::inst());

        if (!($msgObj instanceof DiscussionMsg)) {
            return 'Ошибка добавления сообщения';
        }

        return new AjaxSuccess($controller->buildLeaf($msgObj));
    }

}

?>
