<?php

/**
 * Форма изменения регистрационных данных
 *
 */
require_once(dirname(__DIR__) . '/RegForm/RegFormData.php');

class FORM_RegEditForm extends BaseAjaxForm {

    const BUTTON_SAVE = 'Сохранить';

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_AUTHORIZED;
    }

    protected function processImpl(PostArrayAdapter $adapter, $button) {
        $data = new RegFormData();
        /*
         * Имя пользователя
         */
        $name = $adapter->str(FORM_PARAM_REG_NAME);
        $error = UserInputValidator::validateShortText($name);
        if ($error) {
            return array(FORM_PARAM_REG_NAME => $error);
        }
        $name = UserInputTools::safeShortText($name);
        $data->setUserName($name);

        /*
         * Пол
         */
        $sex = $adapter->int(FORM_PARAM_REG_SEX);
        $error = UserInputValidator::validateSex($sex);
        if ($error) {
            return array(FORM_PARAM_REG_SEX => $error);
        }
        $data->setSex($sex);

        /*
         * Обо мне
         */
        $about = $adapter->str(FORM_PARAM_REG_ABOUT);
        if ($about) {
            $error = UserInputValidator::validateLongText($about, false);
            if ($error) {
                return array(FORM_PARAM_REG_ABOUT => $error);
            }
            $data->setAboutSrc($about);
            $data->setAbout(UserInputTools::safeLongText($about));
        }

        /*
         * Контакты
         */
        $contacts = $adapter->str(FORM_PARAM_REG_CONTACTS);
        if ($contacts) {
            $error = UserInputValidator::validateLongText($contacts, false);
            if ($error) {
                return array(FORM_PARAM_REG_CONTACTS => $error);
            }
            $data->setContactsSrc($contacts);
            $data->setContacts(UserInputTools::safeLongText($contacts));
        }

        /*
         * Цитата
         */
        $msg = $adapter->str(FORM_PARAM_REG_MSG);
        if ($msg) {
            $error = UserInputValidator::validateLongText($msg, false);
            if ($error) {
                return array(FORM_PARAM_REG_MSG => $error);
            }
            $data->setMsgSrc($msg);
            $data->setMsg(UserInputTools::safeLongText($msg));
        }

        PsUser::inst()->updateInfo($data);

        return new AjaxSuccess();
    }

    public function getDataImpl() {
        $data = new RegFormData();

        /*
         * Имя пользователя
         */
        $name = $adapter->str(FORM_PARAM_REG_NAME);
        $error = UserInputValidator::validateShortText($name);
        if ($error) {
            return array(FORM_PARAM_REG_NAME => $error);
        }
        $name = UserInputTools::safeShortText($name);
        $data->setUserName($name);


        /*
         * Пол
         */
        $sex = $adapter->str(FORM_PARAM_REG_SEX);
        $error = UserInputValidator::validateSex($sex);
        if ($error) {
            return array(FORM_PARAM_REG_SEX => $error);
        }
        $data->setSex($sex);


        /*
         * Обо мне
         */
        $about = $adapter->str(FORM_PARAM_REG_ABOUT);
        if ($about) {
            $error = UserInputValidator::validateLongText($about, false);
            if ($error) {
                return array(FORM_PARAM_REG_ABOUT => $error);
            }
            $data->setAboutSrc($about);
            $data->setAbout(UserInputTools::safeLongText($about));
        }

        /*
         * Контакты
         */
        $contacts = $adapter->str(FORM_PARAM_REG_CONTACTS);
        if ($contacts) {
            $error = UserInputValidator::validateLongText($contacts, false);
            if ($error) {
                return array(FORM_PARAM_REG_CONTACTS => $error);
            }
            $data->setContactsSrc($contacts);
            $data->setContacts(UserInputTools::safeLongText($contacts));
        }

        /*
         * Цитата
         */
        $msg = $adapter->str(FORM_PARAM_REG_MSG);
        if ($msg) {
            $error = UserInputValidator::validateLongText($msg, false);
            if ($error) {
                return array(FORM_PARAM_REG_MSG => $error);
            }
            $data->setMsgSrc($msg);
            $data->setMsg(UserInputTools::safeLongText($msg));
        }

        return $data;
    }

}

?>
