<?php

class FORM_AdminFoldingEditForm extends BaseAjaxForm {

    protected $CAN_RESET = false;

    const BUTTON_SAVE = 'Сохранить';
    const BUTTON_DELETE = 'Удалить';
    const BUTTON_DELETE_ALL = 'Удалить с записью из БД';

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_AUTHORIZED_AS_ADMIN;
    }

    protected function processImpl(PostArrayAdapter $adapter, $button) {
        //return print_r($adapter->data, true);
        if (!$adapter->hasAllNoEmpty(array('ftype', 'fident'))) {
            return 'Не переданы все необходимые параметры.';
        }

        $ftype = $adapter->str('ftype');
        $fsubtype = $adapter->str('fsubtype');
        $ident = $adapter->str('fident');

        $folding = Handlers::getInstance()->getFolding($ftype, $fsubtype);
        $fEntity = $folding->getFoldedEntity($ident, true);

        $result = 'OK';

        switch ($button) {
            case self::BUTTON_SAVE:
                /*
                 * На всякий случай убедимся, что нам передали все данные с формы
                 */
                check_condition($adapter->hasAll($folding->getAllowedResourceTypes()), 'Переданы не все типы данных для фолдинга');

                //Основим обложку, если фолдинг с ней работает
                if ($folding->isImagesFactoryEnabled()) {
                    $cover = SimpleUploader::inst()->saveUploadedFile(false);
                    if ($cover) {
                        $folding->updateEntityCover($ident, $cover);
                        $cover->remove();
                    }
                }
                //Обновим остальные параемтры
                $folding->editEntity($ident, $adapter);
                break;

            case self::BUTTON_DELETE_ALL:
                //Удаляем строку из базы
                TableExporter::inst()->getTable($folding->getTableName())->deleteFoldingDbRec($folding, $ident);
            case self::BUTTON_DELETE:
                $folding->deleteEntity($ident);
                $result = AP_APFoldingEdit::urlFoldingEntitys($folding);
                break;
        }

        return new AjaxSuccess($result);
    }

}

?>