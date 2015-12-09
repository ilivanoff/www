<?php

/**
 * Форма обратной связи
 */
class FORM_FeedbackForm extends BaseAjaxForm implements CheckActivityForm, CheckCaptureForm {

    const BUTTON_SEND = 'Отправить';

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    protected function processImpl(PostArrayAdapter $adapter, $button) {
        $FEEDBACK = FeedbackManager::inst();

        /*
         * Тема
         */
        $theme = $adapter->str(FORM_PARAM_THEME);
        $error = UserInputValidator::validateShortText($theme);
        if ($error) {
            return array(FORM_PARAM_THEME => $error);
        }
        $theme = UserInputTools::safeShortText($theme);

        /*
         * Комментарий
         */
        $text = $adapter->str(FORM_PARAM_COMMENT);
        $error = UserInputValidator::validateLongText($text);
        if ($error) {
            return array(FORM_PARAM_COMMENT => $error);
        }
        $text = UserInputTools::safeLongText($text);

        /*
         * АВТОРИЗОВАН - пользуемся стандартным механизмом добавления сообщения в тред.
         * Кодом треда, при этом, является сам пользователь.
         */
        if (AuthManager::isAuthorized()) {
            $msg = $FEEDBACK->saveMessage(PsUser::inst()->getId(), null, $text, $theme, PsUser::inst());
            return new AjaxSuccess($FEEDBACK->buildLeaf($msg));
        }

        /*
         * НЕ АВТОРИЗОВАН - сохраняем сообщение в таблицу анонимных пользователей.
         */
        if (!AuthManager::isAuthorized()) {
            /*
             * Имя пользователя
             */
            $name = $adapter->str(FORM_PARAM_NAME);
            $error = UserInputValidator::validateShortText($name);
            if ($error) {
                return array(FORM_PARAM_NAME => $error);
            }
            $name = UserInputTools::safeShortText($name);

            /*
             * Контакты
             */
            $contacts = $adapter->str(FORM_PARAM_REG_CONTACTS);
            if ($contacts) {
                $error = UserInputValidator::validateShortText($contacts, false);
                if ($error) {
                    return array(FORM_PARAM_REG_CONTACTS => $error);
                }
                $contacts = UserInputTools::safeShortText($contacts);
            }

            $FEEDBACK->saveAnonimousFeedback($name, $contacts, $theme, $text);
            return new AjaxSuccess();
        }
    }

}

?>