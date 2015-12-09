<?php

/**
 * Форма смены временной зоны
 *
 */
class FORM_TzEditForm extends BaseAjaxForm {

    const BUTTON_SAVE = 'Сохранить';

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_AUTHORIZED;
    }

    protected function processImpl(PostArrayAdapter $adapter, $button) {
        $tzName = $adapter->str(FORM_PARAM_TIMEZONE);

        if (!$tzName) {
            return array(FORM_PARAM_TIMEZONE => 'required');
        }

        if (!PsTimeZone::inst()->isTimeZoneExists($tzName)) {
            return array(FORM_PARAM_TIMEZONE => "Временная зона [$tzName] не существует");
        }

        PsUser::inst()->updateTimezone($tzName);

        return new AjaxSuccess();
    }

}

?>