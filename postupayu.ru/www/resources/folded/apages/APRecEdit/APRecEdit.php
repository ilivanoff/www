<?php

class AP_APRecEdit extends BaseAdminPage {
    /**
     * Возможные режимы работы
     */

    const MODE_TABLES_LIST = 'list';
    const MODE_TABLE_ROWS = 'rows';
    const MODE_TABLE_SQL = 'sql';
    const MODE_TABLE_ARR = 'arr';
    const MODE_ROW_ADD = 'add';
    const MODE_ROW_EDIT = 'edit';
    const MODE_ROW_DELETE = 'delete';
    const MODE_INSERTS = 'inserts';

    /**
     * GET параметры. Нужно называть аккуратно, так как может быть пересечение с $_POST параметрами формы
     */
    public function title() {
        $cnt = TableExporter::inst()->getModifiedTablesCount();
        return 'Работа с таблицами БД' . ($cnt ? ' ' . PsHtml::span(array('class' => 'small'), "($cnt)") : '');
    }

    /**
     * Различные ссылки, которые могут быть обработаны данной страницей
     */
    private static function url($mode = self::MODE_TABLES_LIST, $table = null, $recOrId = null, array $params = array()) {
        $params['mode'] = $mode;

        if (in_array($mode, array(self::MODE_TABLES_LIST, self::MODE_INSERTS))) {
            return self::pageUrl($params);
        }

        $table = $table instanceof PsTable ? $table : TableExporter::inst()->getTable($table);
        $params['table'] = $table->getName();
        if (in_array($mode, array(self::MODE_TABLE_ROWS, self::MODE_ROW_ADD, self::MODE_TABLE_SQL, self::MODE_TABLE_ARR))) {
            return self::pageUrl($params);
        }

        $pk = $table->getPk()->getName();
        $recId = is_array($recOrId) ? array_get_value($pk, $recOrId) : $recOrId;
        $params[$pk] = $recId;

        return self::pageUrl($params);
    }

    public static function urlTables() {
        return self::url();
    }

    public static function urlInserts() {
        return self::url(self::MODE_INSERTS);
    }

    public static function urlTableRows($table) {
        return self::url(self::MODE_TABLE_ROWS, $table);
    }

    public static function urlTableSql($table) {
        return self::url(self::MODE_TABLE_SQL, $table);
    }

    public static function urlTableArr($table) {
        return self::url(self::MODE_TABLE_ARR, $table);
    }

    public static function urlRecAdd($table) {
        return self::url(self::MODE_ROW_ADD, $table);
    }

    public static function urlRecFolding($ftype, $fsubtype, $fident) {
        $folding = Handlers::getInstance()->getFolding($ftype, $fsubtype);
        $params['ftype'] = $ftype;
        $params['fsubtype'] = $fsubtype;
        $params['fident'] = $fident;
        return self::url(self::MODE_ROW_ADD, $folding->getTableName(), null, $params);
    }

    public static function urlRecEdit($table, $rec) {
        return self::url(self::MODE_ROW_EDIT, $table, $rec);
    }

    public static function urlRecDelete($table, $rec) {
        return self::url(self::MODE_ROW_DELETE, $table, $rec);
    }

    /**
     * Обработка действия формы создания/изменения/удаления записи
     */
    private function processForm(RecEditFormData $data) {
        $table = $data->getTable();
        $action = $data->getAction();
        $rec = $data->getRec();

        /*
         * Сначала выполним действия с фолдингом, так как они более багоёмки
         */
        if ($data->isProcessFolding()) {
            $foldingEntity = $table->getFoldingEntity4DbRecAnyway($rec);
            check_condition($foldingEntity, 'Не удалось определить фолдинг для редактируемой сущности.');
            $folding = $foldingEntity->getFolding();
            $fident = $foldingEntity->getIdent();

            /**
             * Если мы создавали или модифицировали запись, то фолдинг нужно создать.
             * Если удаляли - то фолдинг нужно удалить.
             */
            switch ($action) {
                case PS_ACTION_CREATE:
                case PS_ACTION_EDIT:
                    $folding->createEntity($fident);
                    break;
                case PS_ACTION_DELETE:
                    $folding->deleteEntity($fident);
                    break;
            }
        }

        /*
         * Сохраним изменения в БД
         */
        $table->saveRec($rec, $action);

        if ($action == PS_ACTION_EDIT) {
            PsUtil::redirectToSelf();
        } else {
            PsUtil::redirectTo(self::urlTableRows($table));
        }
    }

    public function buildContent() {
        PsDefines::setReplaceFormulesWithImages(false);

        $navigation = AdminPageNavigation::inst();

        /*
         * Инициализируем необходимые менеджеры
         */
        $TE = TableExporter::inst();
        $RQ = GetArrayAdapter::inst();
        $FORM = FORM_RecEditForm::getInstance();
        $TABLES = PsTable::configured();

        /*
         * Инициализируем параметры, которые нужно будет передать smarty
         */
        $PARAMS['mode'] = null;
        $PARAMS['table'] = null;
        $PARAMS['error'] = null;
        $PARAMS['errors'] = PsDbIniHelper::validateAll();

        /*
         * Обработаем форму
         */
        try {
            if ($FORM->isValid4Process()) {
                $this->processForm($FORM->getData());
            } else if ($FORM->isErrorOccurred()) {
                $PARAMS['error'] = PsHtml::divErr($FORM->getError());
            }
        } catch (Exception $e) {
            $PARAMS['error'] = ExceptionHandler::getHtml($e);
        }


        /*
         * Обработаем параметры и определим режим работы
         */
        $MODE = $RQ->str('mode', self::MODE_TABLES_LIST);

        /** @var PsTable */
        $TABLE = null; // Таблица
        $ROW = null;   // Редактируемая строка

        switch ($MODE) {
            case self::MODE_ROW_ADD:
                //Если передан фолдинг, то подставим в форму создания его параметры
                $folding = Handlers::getInstance()->getFolding($RQ->str('ftype'), $RQ->str('fsubtype'), false);
                $fident = $RQ->str('fident');
                if ($folding && $folding->getTableName() && $fident) {
                    $TABLE = $TE->getTable($folding);
                    $ROW = $folding->getDbRec4Entity($fident);
                    break;
                }

                $TABLE = PsTable::inst($RQ->str('table'));

                /*
                 * Нам не удалось определить внешний вид создаваемой строки, 
                 * но если у данной таблицы один фолдинг - возмём вид строки у него.
                 */
                $folding = $TABLE->getSingleFolding();
                $ROW = $folding ? $folding->getDbRec4Entity($folding->getNextEntityIdent()) : null;
                break;
            case self::MODE_TABLE_ROWS:
            case self::MODE_TABLE_SQL:
            case self::MODE_TABLE_ARR:
            case self::MODE_ROW_EDIT:
            case self::MODE_ROW_DELETE:
                $TABLE = PsTable::inst($RQ->str('table'));
                switch ($MODE) {
                    case self::MODE_ROW_EDIT:
                    case self::MODE_ROW_DELETE:
                        $ROW = $TABLE->getRow($RQ->int($TABLE->getPk()->getName()));
                        break;
                }
                break;
            case self::MODE_INSERTS:
                //Nothing to do
                break;
            default:
                //Защитимся от некорректного значения параметра 'mode'
                $MODE = self::MODE_TABLES_LIST;
                break;
        }

        $PARAMS['mode'] = $MODE;
        $PARAMS['table'] = $TABLE;

        /*
         * ВЫПОЛНЯЕМ ОБРАБОТКУ
         */

        switch ($MODE) {
            case self::MODE_TABLES_LIST:
                $PARAMS['tables'] = $TABLES;
                $navigation->setCurrent('Список таблиц');
                break;

            case self::MODE_INSERTS:
                $PARAMS['tables'] = $TABLES;
                $navigation->addPath(self::urlTables(), 'Список таблиц');
                $navigation->setCurrent('Вставка данных');
                break;

            case self::MODE_TABLE_ROWS:
                $PARAMS['rows'] = $TABLE->getRows();
                $PARAMS['addurl'] = self::urlRecAdd($TABLE);
                $PARAMS['modified'] = $TABLE->getModifiedRows();

            case self::MODE_TABLE_SQL:
            case self::MODE_TABLE_ARR:
                $navigation->addPath(self::urlTables(), 'Список таблиц');
                $navigation->setCurrent($TABLE->getName());
                break;

            default:
                switch ($MODE) {
                    /*
                     * Помимо действий над записью, мы ещё раздиляем действия над фолдингами 
                     * (если они есть для таблицы). Поэтому мы установм: 
                     * FormAction - для типа редактирования записи
                     * FormButton - для типа работы с фолдингом
                     */
                    case self::MODE_ROW_ADD:
                        $FORM->setFormAction(PS_ACTION_CREATE);
                        $FORM->setButtons(FORM_RecEditForm::BUTTON_CREATE);
                        if ($TABLE->hasFoldings()) {
                            $FORM->addButton(FORM_RecEditForm::BUTTON_CREATEF);
                        }
                        $navigation->setCurrent('Создание записи');
                        break;
                    case self::MODE_ROW_EDIT:
                        $FORM->setFormAction(PS_ACTION_EDIT);
                        $FORM->setButtons(FORM_RecEditForm::BUTTON_EDIT);
                        if ($TABLE->hasFoldings() && !$TABLE->hasFoldingEntity4DbRec($ROW, true)) {
                            $FORM->addButton(FORM_RecEditForm::BUTTON_EDITF);
                        }

                        $navigation->setCurrent('Редактирование записи');
                        break;
                    case self::MODE_ROW_DELETE:
                        $FORM->setFormAction(PS_ACTION_DELETE);
                        $FORM->setButtons(FORM_RecEditForm::BUTTON_DELETE);
                        if ($TABLE->hasFoldingEntity4DbRec($ROW, true)) {
                            $FORM->addButton(FORM_RecEditForm::BUTTON_DELETEF);
                        }

                        $navigation->setCurrent('Удаление записи');
                        break;
                    default:
                        raise_error("Неизвестный режим: [$MODE]");
                }

                $FORM->setHidden('table', $TABLE->getName());

                $FORM->setSmartyParam('table', $TABLE);
                $FORM->setSmartyParam('rec', $ROW);

                $navigation->addPath(self::urlTables(), 'Список таблиц');
                $navigation->addPath(self::urlTableRows($TABLE), $TABLE->getName());

                break;
        }

        echo $this->getFoldedEntity()->fetchTpl($PARAMS);
    }

    public function getSmartyParams4Resources() {
        return array('MATHJAX_DISABLE' => true);
    }

}

?>