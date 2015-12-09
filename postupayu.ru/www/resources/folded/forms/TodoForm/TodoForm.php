<?php

/**
 * Форма редактирования списка задач
 *
 * @author Admin
 */
class FORM_TodoForm extends BaseAjaxForm {

    const BUTTON_SAVE = 'Сохранить';

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    protected function processImpl(PostArrayAdapter $adapter, $button) {
        PsDefines::assertProductionOff(__CLASS__);
        $text = $adapter->str('text');
        if (!$text) {
            return array('text', 'required');
        }
        $mtime = $adapter->int('mtime');

        ToDoFile::inst()->save($text, $mtime);

        return new AjaxSuccess();
    }

}

?>