<?php

class PsTable extends BaseDataStore {

    private $columns; //Список колонок таблицы
    private $columnsConfigurable; //Список колонок, которые можно конфигурировать
    private $pk; //Колонка - первичный ключ
    private $pkEqualents;
    private $title;
    private $take4exportColumns;
    private $canBeManuallyRestrictedColumns;

    /** @return PsTable */
    private function load() {
        if (!is_array($this->columns)) {
            $this->columns = AdminDbBean::inst()->getColumns($this->getName());

            $this->columnsConfigurable = $this->columns;

            $this->pk = null;
            $this->title = null;
            $this->pkEqualents = array();
            $this->take4exportColumns = array();

            $titleCodeWords = array('name', 'title');

            /* @var $col PsTableColumn */
            foreach ($this->columns as $col) {
                if ($col->isPk()) {
                    $this->pk = $col;

                    /*
                     * Если первичный ключ является автоинкрементальным полем - 
                     * исключим его из конфигурируемых колонок.
                     */
                    if ($col->isAi()) {
                        unset($this->columnsConfigurable[$col->getName()]);
                    }
                }

                if (!$col->isPk() && $col->isPkEquivalent()) {
                    $this->pkEqualents[$col->getName()] = $col;
                }

                if (!$this->title) {
                    if (contains_substring($col->getName(), $titleCodeWords)) {
                        $this->title = $col;
                    }
                }

                if ($col->isTake4Export()) {
                    $this->take4exportColumns[$col->getName()] = $col;
                }

                if ($col->isCanBeManuallyRestricted()) {
                    $this->canBeManuallyRestrictedColumns[$col->getName()] = $col;
                }
            }

            //Если не определили titleColumn, то возмём первичный ключ
            $this->title = $this->title ? $this->title : $this->pk;
        }
        return $this;
    }

    public function getName() {
        return $this->TABLE_NAME;
    }

    public function getComment() {
        return $this->TABLE_COMMENT;
    }

    public function getColumns() {
        return $this->load()->columns;
    }

    public function hasColumn($colName) {
        return array_key_exists($colName, $this->getColumns());
    }

    public function getColumnsConfigurable() {
        return $this->load()->columnsConfigurable;
    }

    /**
     * Метод возвращает все настройки, которые могут быть заданы для данной таблицы
     */
    public function getAllowedTableProperties() {
        return PsTableColumnProps::getAllowedTableProperties($this);
    }

    /**
     * Метод возвращает все настройки столбцов, которые могут быть заданы для данной таблицы
     */
    public function getAllowedColumnProperties() {
        return PsTableColumnProps::getAllowedColumnProperties($this);
    }

    /** @return PsTableColumn */
    public function getColumn($column) {
        return check_condition(array_get_value($column, $this->getColumns()), "Столбец {$this->getName()}.$column не существует.");
    }

    /** @return PsTableColumn */
    public function getPk() {
        return $this->load()->pk;
    }

    public function hasPk() {
        return !!$this->getPk();
    }

    //Проверка - является ли первичный ключ auto_increment полем
    public function isPkAi() {
        return $this->hasPk() && $this->getPk()->isAi();
    }

    /** @return PsTableColumn */
    public function getTitleColumn() {
        return $this->load()->title;
    }

    private function getPkEqualentColumns($check = true) {
        $eq = $this->load()->pkEqualents;
        //check_condition(!$check || !$this->pk->isAi() || !empty($eq), "Первичный ключ {$this->getName()}.{$this->pk->getName()} является autoincrement полем, и при этом не имеет альтернативы");
        return $eq;
    }

    public function getTake4ExportColumns() {
        return $this->load()->take4exportColumns;
    }

    public function getManuallyRestrictedColumns() {
        return $this->load()->canBeManuallyRestrictedColumns;
    }

    /**
     * Проверка, относится ли таблица к SDK
     */
    public function isSdk() {
        return DbIni::isSdkTable($this->getName());
    }

    /**
     * Проверка, относится ли таблица к SDK
     */
    public function getScope() {
        return $this->isSdk() ? ENTITY_SCOPE_SDK : ENTITY_SCOPE_PROJ;
    }

    /**
     * Проверка - задана ли настройка для таблицы
     */
    public function isProperty($colProperty) {
        return PsTableColumnProps::valueOf($colProperty)->isTableHasProperty($this->getName());
    }

    /**
     * Признак - сконфигурирована ли таблица
     */
    public function isConfigured() {
        return $this->isProperty(PsTableColumnProps::TABLE_CONFIGURED());
    }

    /**
     * Признак - является ли таблица редактируемой
     */
    public function isEditable() {
        return $this->isConfigured();
    }

    /**
     * Признак - синхранизируется ли таблица с файлом
     */
    public function isFilesync() {
        return $this->isConfigured() && $this->isProperty(PsTableColumnProps::TABLE_FILESYNC());
    }

    /**
     * Зависимые группы кешей
     */
    private $cacheGroups;

    public function getDependableCaches() {
        return $this->cacheGroups = is_array($this->cacheGroups) ? $this->cacheGroups : AdminDbBean::inst()->getTableDependableCaches($this->getName());
    }

    public function isDependableCache($group) {
        return in_array($group, $this->getDependableCaches());
    }

    /**
     * Список триггеров таблицы
     */
    private $triggers;

    public function getTriggers() {
        return $this->triggers = is_array($this->triggers) ? $this->triggers : AdminDbBean::inst()->getTableTriggers($this->getName());
    }

    public function hasTriggers() {
        return count($this->getTriggers()) > 0;
    }

    /**
     * update
     */
    private function executeUpdate($query) {
        return PSDB::update($query) > 0;
    }

    /**
     * insert
     */
    private function executeInsert($query) {
        return PSDB::insert($query);
    }

    /**
     * Список записей из таблицы для построения комбо-бокса
     */
    private $selects;

    public function getSelectOptions() {
        return is_array($this->selects) ? $this->selects : $this->selects = AdminDbBean::inst()->getTableDataAsOptions($this->getName(), $this->getPk()->getName(), $this->getTitleColumn()->getName());
    }

    /** Выражение для ограничения where в строке */
    private function rowWhereExpr(array $row, $canUseAiPk = false) {
        //Проверим, может ли быть идентификатором первичный ключ
        $pk = $this->getPk();
        check_condition($pk, $this->getName() . ' dont have PK');
        $pkName = $pk->getName();

        if ((!$pk->isAi() || $canUseAiPk) && array_key_exists($pkName, $row)) {
            return "$pkName=" . $pk->safe4insert($row[$pkName]);
        }

        $tokens = array();
        /* @var $col PsTableColumn */
        foreach ($this->getPkEqualentColumns() as $id => $col) {
            if (!array_key_exists($id, $row)) {
                return null;
            }
            $tokens[] = "$id=" . $col->safe4insert($row[$id]);
        }
        return empty($tokens) ? null : implode(' and ', $tokens);
    }

    /** Уникальный код строки. Если нет всех необходимых ключёй - вернётся null */
    private function rowKey(array $row) {
        return md5($this->rowWhereExpr($row, false));
    }

    /**
     * Возвращает замену первичного ключа для данной таблицы, если этот первичный ключ является autoincrenemt полем
     * 1
     * or
     * selec id form table where ident='five'
     */
    public function getPkReplacement($rowId) {
        $pk = $this->getPk();
        check_condition($pk, $this->getName() . ' dont have PK');
        if (!$pk->isAi()) {
            //Первичный ключ - не autoincrement поле
            return $rowId;
        }
        $rec = $this->getRow($rowId);
        $where = $this->rowWhereExpr($rec, false);

        $pkName = $pk->getName();
        $tableName = $this->getName();

        return "select $pkName from $tableName where $where";
    }

    /**
     * Построение sql запроса для создания/изменения записи
     * 
     * $canUsePk - признак, можно ли использовать первичный ключ при вставке. Если false, но не будет попытки замены ПК.
     */
    private function getSql(array $row, $action, $canUseAiPk = true, array $currentRow = array()) {
        switch ($action) {
            case PS_ACTION_CREATE:
                $finalCols = array();
                $finalData = array();
                /* @var $col PsTableColumn */
                foreach ($this->getColumns() as $id => $col) {
                    if ($col->isUseOn($action) && array_key_exists($id, $row)) {
                        $finalCols[] = $id;
                        $finalData[] = $col->safe4insert($row[$id]);
                    }
                }
                return 'insert into ' . $this->getName() . ' (' . implode(', ', $finalCols) . ') values (' . implode(', ', $finalData) . ')';

            case PS_ACTION_EDIT:
                $tokens = array();
                /* @var $col PsTableColumn */
                foreach ($this->getColumns() as $id => $col) {
                    if ($col->isPk()) {
                        continue;
                    }
                    if ($col->isUseOn($action) && array_key_exists($id, $row)) {
                        if (!array_key_exists($id, $currentRow) || ($currentRow[$id] !== $row[$id])) {
                            $tokens[] = "$id=" . $col->safe4insert($row[$id]);
                        }
                    }
                }
                return 'update ' . $this->getName() . ' set ' . implode(', ', $tokens) . ' where ' . $this->rowWhereExpr($row, $canUseAiPk);

            case PS_ACTION_DELETE:
                return 'delete from ' . $this->getName() . ' where ' . $this->rowWhereExpr($row, $canUseAiPk);
        }
    }

    /**
     * Выполняет создание/обновление записи, возвращая её код
     */
    public function saveRec(array $rec, $action) {
        $sql = $this->getSql($rec, $action);
        $pk = $this->getPk();
        if ($action == PS_ACTION_CREATE && $pk->isAi()) {
            //В случае, если поле - autoincrement, то айдишник не вернётся
            return $this->executeInsert($sql);
        } else {
            $this->executeUpdate($sql);
            return array_get_value($pk->getName(), $rec);
        }
    }

    /**
     * Выполняет загрузку всех строк из таблицы
     */
    public function getRows() {
        return PSDB::getArray('select * from ' . $this->getName());
    }

    /**
     * Выполняет загрузку строки из таблицы
     */
    public function getRow($rowOrId, $check = true) {
        $tableName = $this->getName();
        $pkName = $this->getPk()->getName();
        $rowId = is_array($rowOrId) ? array_get_value($pkName, $rowOrId) : $rowOrId;
        if (is_numeric($rowId)) {
            $rowId = 1 * $rowId;
            $row = PSDB::getRec("select * from $tableName where $pkName=?", $rowId);
            check_condition(!$check || is_array($row), "Не найдена запись $tableName ($pkName=$rowId)");
            return $row;
        }
        if (is_array($rowOrId)) {
            //Ищем по параметрам строки
            $where = $this->rowWhereExpr($rowOrId, true);
            $row = PSDB::getRec("select * from $tableName where $where");
            check_condition(!$check || is_array($row), "Не найдена запись $tableName ($where)");
            return $row;
        }
        raise_error('Некорректные условия поиска строки в таблице ' . $tableName);
    }

    /**
     * Обрабатывает сохраняемую строку из формы. Всё, что передаётся (hidden+editable) - будет взято, как есть.
     * Всё, что исключается, будет дополнено данными из базы.
     * 
     * Если какое-либо из полей не проходит валидацию - будет возвращена ошибка.
     * 
     * @param type $row - сохраняемые данные c формы
     * @param type $action - действие формы
     */
    public function fetchRowFromForm(array $formRow, $action) {
        $dbRow = array();
        switch ($action) {
            case PS_ACTION_CREATE:
                //Данных в базе нет
                break;

            case PS_ACTION_EDIT:
            case PS_ACTION_DELETE:
                //Данные в базе должны быть
                $dbRow = $this->getRow($formRow);
                break;
        }

        $fetched = array();
        /* @var $col PsTableColumn */
        foreach ($this->getColumns() as $id => $col) {
            if ($col->isUseOn($action)) {
                //Столбец должен прийти с формы
                check_condition(array_key_exists($id, $formRow), "Столбец $id долеж был прийти с формы");
                //Валидируем
                $formVal = $formRow[$id];
                $err = $col->validateFromForm($formVal, $action);
                if ($err) {
                    return "Поле [$id] имеет некорректное значение [" . htmlspecialchars($formVal) . "]: $err";
                }
                $fetched[$id] = $formVal;
            } else {
                //Столбец должен быть взят из базы
                if ($action != PS_ACTION_CREATE) {
                    check_condition(array_key_exists($id, $dbRow), "Столбец $id не был загружен из базы");
                    $fetched[$id] = $dbRow[$id];
                }
            }
        }

        return $fetched;
    }

    /**
     * Метод возвращает строку из базы, соотвутствующую переданной сущности фолдинга
     */
    public function getFoldingDbRec(FoldedResources $folding, $ident) {
        check_condition($folding->getTableName() === $this->getName(), "Таблица {$this->getName()} не работает с фолдингом {$folding->getEntityName()}.");
        $virtualRow = $folding->getDbRec4Entity($ident);
        return is_array($virtualRow) ? $this->getRow($virtualRow, false) : null;
    }

    /**
     * Метод создаёт строку в базе, соотвутствующую переданной сущности фолдинга
     */
    public function createFoldingDbRec(FoldedResources $folding, $ident) {
        check_condition($folding->getTableName() === $this->getName(), "Таблица {$this->getName()} не работает с фолдингом {$folding->getEntityName()}.");
        $dbrec = $this->getFoldingDbRec($folding, $ident);
        if (!is_array($dbrec)) {
            $this->saveRec($folding->getDbRec4Entity($ident), PS_ACTION_CREATE);
        }
    }

    /**
     * Метод удаляет строку из базы, соотвутствующую переданной сущности фолдинга
     */
    public function deleteFoldingDbRec(FoldedResources $folding, $ident) {
        check_condition($folding->getTableName() === $this->getName(), "Таблица {$this->getName()} не работает с фолдингом {$folding->getEntityName()}.");
        $dbrec = $this->getFoldingDbRec($folding, $ident);
        if (is_array($dbrec)) {
            $this->saveRec($folding->getDbRec4Entity($ident), PS_ACTION_DELETE);
        }
    }

    /**
     * Фозвращает фолдинги, хранимые в этой таблице
     */
    public function getFoldings() {
        return FoldedResourcesManager::inst()->getTableFoldings($this->getName());
    }

    /**
     * Если с данной таблицей работает единственный фолдинг - вернёт его
     * 
     * @return FoldedResources
     */
    public function getSingleFolding() {
        $foldings = $this->getFoldings();
        return count($foldings) == 1 ? end($foldings) : null;
    }

    public function hasFoldings() {
        return count($this->getFoldings()) > 0;
    }

    /**
     * Пробегает по всем фолдингам, хранимым в данной таблице, и определяет ту сущность фолдинга, 
     * которая соответствует данной записи в таблице.
     * 
     * @return FoldedEntity
     */
    public function getFoldingEntity4DbRec(array $dbRec, $checkExists) {
        /* @var $folding FoldedResources */
        foreach ($this->getFoldings() as $folding) {
            $ident = $folding->getEntityIdent4DbRec($dbRec, $checkExists);
            if ($ident) {
                return $folding->getFoldedEntity($ident);
            }
        }
        return null;
    }

    /**
     * Пробегает по всем фолдингам, хранимым в данной таблице, и определяет ту сущность фолдинга, 
     * которая соответствует данной записи в таблице.
     * 
     * @return FoldedEntity
     */
    public function getFoldingEntity4DbRecAnyway(array $dbRec) {
        /* @var $folding FoldedResources */
        foreach ($this->getFoldings() as $folding) {
            $ident = $folding->getEntityIdent4DbRec($dbRec, false);
            if ($ident) {
                return $folding->getFoldedEntityAnyway($ident);
            }
        }
        return null;
    }

    public function hasFoldingEntity4DbRec(array $dbRec, $checkExists) {
        return is_object($this->getFoldingEntity4DbRec($dbRec, $checkExists));
    }

    /*
     * ЗАГРУЗКА/ВЫГРУЗКА данных из файла
     */

    /** @return DirItem */
    private function storeFile($ext = 'data') {
        return DirManager::database()->getDirItem(null, $this->getName(), $ext);
    }

    /**
     * Выгружает данные таблицы в файл
     */
    public function exportDataToFile($returnOnly = false) {
        $rows = array();
        foreach ($this->getRows() as $row) {
            $savedRow = array();
            /** @var PsTableColumn */
            foreach ($this->getTake4ExportColumns() as $id => $col) {
                $savedRow[$id] = $col->safe4export($row[$id]);
            }
            $rows[] = $savedRow;
        }

        if (!$returnOnly) {
            $this->storeFile()->saveArrayToFile($rows);
        }

        return $rows;
    }

    public function getDataFromFile() {
        return $this->storeFile()->getArrayFromFile();
    }

    /**
     * Типы изменённых данных
     */

    const DIFF_FILE_ONLY = 'F'; //Есть только в файле
    const DIFF_MODIFIED = 'M';  //Модифицированные
    const DIFF_DB_ONLY = 'D';   //Есть только в базе

    /**
     * Определяет все различия между файлом и таблицей и приводит в соответствие с файлом:
     * 1. Создаёт те записи, которые есть в файле но нет в базе - F
     * 2. Модифицированные записи проводит в соответствие к фарианту из файла - M
     * 3. Записи, которые есть только в базе, удаляет - D
     * 
     * @param $ident - идентификатор записи, которая будет обновлена.
     */

    private function importDataFromFile($recIdent = null) {
        $accepted = false;
        $modified = $this->getModifiedRows();
        if ($recIdent) {
            //Ищем конкретную запись
            foreach (PsUtil::getClassConsts(__CLASS__, 'DIFF_') as $type) {
                if (array_key_exists($recIdent, $modified[$type])) {
                    //Запись с идентификатором $recIdent изменена с типом $type
                    $this->executeUpdate($modified[$type][$recIdent]['SQL']);
                    $accepted = true;
                    break;
                }
            }
        } else {
            //Принимаем все изменения
            foreach ($modified as /* $type => */ $info) {
                foreach ($info as /* $ident => */ $data) {
                    $this->executeUpdate($data['SQL']);
                    $accepted = true;
                }
            }
        }

        if ($accepted) {
            //Мы приняли одно или более изменений, сбросим полученную ранее разницу
            $this->modifiedRows = null;
        }
    }

    public function acceptDiff($recIdent) {
        check_condition($recIdent, 'Не передан идентификатор обновляемой записи.');
        $this->importDataFromFile($recIdent);
    }

    public function acceptAllDiffs() {
        $this->importDataFromFile();
    }

    /**
     * Получает разницу между текущим состоянием базы и состоянием в файле
     */
    private $modifiedRows = null;

    /**
     * Возвращает разницу между содержимым базы и содерсимым файла
     */
    public function getModifiedRows() {
        if (is_array($this->modifiedRows)) {
            return $this->modifiedRows;
        }

        $this->modifiedRows[self::DIFF_FILE_ONLY] = array(); //Есть только в файле
        $this->modifiedRows[self::DIFF_MODIFIED] = array();  //Модифицированные
        $this->modifiedRows[self::DIFF_DB_ONLY] = array();   //Есть только в базе

        /*
         * 
         */

        //1. Если файла нет, то интерпретируем это, как будто в нём нет записей
        $dataFromFile = to_array($this->getDataFromFile());

        //2. Есть разница в данных, определим её.
        //2.1 ФАЙЛ
        $fileMap = array();

        foreach ($dataFromFile as $row) {
            $rowKey = $this->rowKey($row);
            if (!$rowKey) {
                //Для строки не удалось построить идентификатор, пропускаем
                return $this->modifiedRows;
            }
            $fileMap[$rowKey] = array();
            /* @var $col PsTableColumn */
            foreach ($this->getTake4ExportColumns() as $id => $col) {
                if (!array_key_exists($id, $row)) {
                    //Добавился экспортируемый столбец в таблицу, но в данных его нет
                    return $this->modifiedRows;
                }
                $fileMap[$rowKey][$id] = $row[$id];
            }
        }

        //2.2 БД
        $dbMap = array();

        $dataFromTable = $this->exportDataToFile(true);

        foreach ($dataFromTable as $row) {
            $rowKey = $this->rowKey($row);
            $dbMap[$rowKey] = array();
            /* @var $col PsTableColumn */
            foreach ($this->getTake4ExportColumns() as $id => $col) {
                $dbMap[$rowKey][$id] = $row[$id];
            }
        }

        //2.3 РАЗНИЦА

        $keys = array_unique(array_merge(array_keys($fileMap), array_keys($dbMap)));

        foreach ($keys as $key) {
            $fileRow = array_get_value($key, $fileMap);
            $dbRow = array_get_value($key, $dbMap);

            if ($fileRow && !$dbRow) {
                $this->modifiedRows[self::DIFF_FILE_ONLY][$key] = array(
                    'ROW' => $fileRow,
                    'SQL' => $this->getSql($fileRow, PS_ACTION_CREATE)
                );
                continue;
            }

            if (!$fileRow && $dbRow) {
                $this->modifiedRows[self::DIFF_DB_ONLY][$key] = array(
                    'ROW' => $dbRow,
                    'SQLI' => $this->getSql($dbRow, PS_ACTION_CREATE),
                    'SQL' => $this->getSql($dbRow, PS_ACTION_DELETE)
                );
                continue;
            }

            if (serialize($fileRow) != serialize($dbRow)) {
                $this->modifiedRows[self::DIFF_MODIFIED][$key] = array(
                    'FROW' => $fileRow,
                    'DROW' => $dbRow,
                    'SQL' => $this->getSql($fileRow, PS_ACTION_EDIT, false, $dbRow)
                );
                continue;
            }
        }

        return $this->modifiedRows;
    }

    public function hasModified($type = null) {
        $modified = $this->getModifiedRows();
        if ($type) {
            return !empty($modified[$type]);
        } else {
            foreach ($modified as $rows) {
                if (!empty($rows)) {
                    return true;
                }
            }
            return false;
        }
    }

    /**
     * Выгружает данные таблицы в виде инсертов
     */
    public function exportDataAsInserts() {
        $inserts = array();
        foreach ($this->getRows() as $row) {
            $inserts[] = $this->getSql($row, PS_ACTION_CREATE);
        }
        return $inserts;
    }

    public function exportDataAsInsertsSql() {
        $glue = ";\n";
        $inserts = implode($glue, $this->exportDataAsInserts());
        return $inserts ? $inserts . $glue : '';
    }

    /**
     * Выгружает данные из файла в виде инсертов
     */
    public function exportFileAsInserts() {
        $inserts = array();
        foreach (to_array($this->getDataFromFile()) as $row) {
            $inserts[] = $this->getSql($row, PS_ACTION_CREATE);
        }
        return $inserts;
    }

    /**
     * Выгружает данные из файла в виде инсертов, склеенных в строку
     */
    public function exportFileAsInsertsSql() {
        $glue = ";\n";
        $inserts = implode($glue, $this->exportFileAsInserts());
        return $inserts ? $inserts . $glue : '';
    }

    /**
     * Экземпляр таблицы по названию
     * 
     * @return PsTable
     */
    public static function inst($name) {
        return AdminDbBean::inst()->getTable($name);
    }

    /**
     * Проверяет, существует ли таблица с заданным названием
     * 
     * @return bool
     */
    public static function exists($name) {
        return AdminDbBean::inst()->existsTable($name);
    }

    /**
     * Все таблицы системы
     */
    public static function all() {
        return AdminDbBean::inst()->getTables();
    }

    /**
     * Редактируемые (сконфигурированные) таблицы
     */
    public static function configured() {
        $cache = AdminDbBean::inst()->getCache();
        if (!$cache->has(AdminDbBean::CACHE_TABLES_CONFIGURED)) {
            $result = array();
            /* @var $table PsTable */
            foreach (self::all() as $tableName => $table) {
                if ($table->isConfigured()) {
                    $result[$tableName] = $table;
                }
            }
            $cache->set(AdminDbBean::CACHE_TABLES_CONFIGURED, $result);
        }
        return $cache->get(AdminDbBean::CACHE_TABLES_CONFIGURED);
    }

    /**
     * Возвращает признак - есть ли таблицы, сконфигурированные некорректно
     */
    public static function hasInvalidConfigured() {
        /* @var $table PsTable */
        foreach (self::configured() as $table) {
            if (!$table->isValidConfigured()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Метод валидирует кастомные настройки таблицы.
     * По сути - проверяет, может ли быть таблица сконфигурирована так, а не иначе.
     */
    public function getConfigErrorsCustom(array $tableProperties) {
        return PsTableColumnProps::validateTablePropertiesCustom($this, $tableProperties);
    }

    /**
     * Метод валидирует текущие настройки таблицы из db.ini.
     */
    public function getConfigErrors() {
        return PsTableColumnProps::validateTableProperties($this);
    }

    /**
     * Метод проверяет, корректно ли сконфигурирована таблица
     * 
     * @return bool
     */
    public function isValidConfigured() {
        return !count($this->getConfigErrors());
    }

}

?>