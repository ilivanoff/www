<?php

/**
 * Форма RecEditForm
 *
 * @author Admin
 */
require_once __DIR__ . '/RecEditFormData.php';

class FORM_RecEditForm extends AbstractForm {

    const BUTTON_CREATE = 'Создать';
    const BUTTON_CREATEF = 'Создать с фолдингом';
    const BUTTON_EDIT = 'Сохранить';
    const BUTTON_EDITF = 'Сохранить и создать фолдинг';
    const BUTTON_DELETE = 'Удалить';
    const BUTTON_DELETEF = 'Удалить с фолдингом';

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_AUTHORIZED_AS_ADMIN;
    }

    protected function processImpl(PostArrayAdapter $aa, $button) {
        $action = $this->getFormAction();
        $table = TableExporter::inst()->getTable($aa->str('table'));
        $rec = $table->fetchRowFromForm($aa->getData(), $action);
        if (!is_array($rec)) {
            return $rec;
        }

        return new RecEditFormData(
                        $table,
                        $action,
                        $this->isSubmittedByButton(array(self::BUTTON_CREATEF, self::BUTTON_EDITF, self::BUTTON_DELETEF)),
                        $rec
        );
    }

}

?>
