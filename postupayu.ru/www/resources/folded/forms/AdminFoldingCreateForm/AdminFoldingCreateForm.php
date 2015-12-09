<?php

class FORM_AdminFoldingCreateForm extends BaseAjaxForm {

    const BUTTON_SAVE = 'Создать';
    const BUTTON_SAVE_DB = 'Создать с записью в БД';

    /**
     * Параметр - идентификатор нового фолдинга
     */
    const PARAM_NEW_FOLDING_IDENT = 'new_folding_ident';

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_AUTHORIZED_AS_ADMIN;
    }

    protected function processImpl(PostArrayAdapter $adapter, $button) {
        if (!$adapter->hasAllNoEmpty('folding', self::PARAM_NEW_FOLDING_IDENT)) {
            return 'Не переданы все необходимые параметры.';
        }

        $funique = $adapter->str('folding');
        $fident = check_condition($adapter->str(self::PARAM_NEW_FOLDING_IDENT), 'Пустой идентификатор фолдинга');

        $folding = Handlers::getInstance()->getFoldingByUnique($funique);
        $folding->assertNotExistsEntity($fident);

        switch ($button) {
            case self::BUTTON_SAVE_DB:
                $action = PS_ACTION_CREATE;
                $table = TableExporter::inst()->getTable($folding);

                //Проставим руками идентификатор фолдинга для колонки из базы
                $adapter->set($folding->getTableColumnIdent(), $fident);

                $rec = $table->fetchRowFromForm($adapter->getData(), $action);
                if (!is_array($rec)) {
                    //Данные для создания записи в БД не прошли валидацию
                    return $rec;
                }
                $table->saveRec($rec, $action);
            //createFoldingDbRec($folding, $ident);
            case self::BUTTON_SAVE:
                $folding->createEntity($fident);
                break;
        }
        return new AjaxSuccess(array('url' => AP_APFoldingEdit::urlFoldingEdit($folding->getFoldedEntity($fident))));
    }

}

?>
