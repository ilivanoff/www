<?php

final class TableExporter extends AbstractSingleton {

    private $TABLES;

    public function getTables($scope = ENTITY_SCOPE_ALL) {
        if (!is_array($this->TABLES)) {
            $this->TABLES[ENTITY_SCOPE_ALL] = array();
            $this->TABLES[ENTITY_SCOPE_SDK] = array();
            $this->TABLES[ENTITY_SCOPE_PROJ] = array();

            $tables = PsTable::all();
            $tablesNotConfigured = array(); //Таблицы, описанные в ini, но не сконфигурированные
            foreach (DbIni::getTables() as $tableName) {
                if (!array_key_exists($tableName, $tables)) {
                    continue; //---
                }
                /* @var $table PsTable */
                $table = $tables[$tableName];

                if ($table->isConfigured()) {
                    $this->TABLES[ENTITY_SCOPE_ALL][$tableName] = $table;
                    $this->TABLES[$table->getScope()][$tableName] = $table;
                    unset($tables[$tableName]);
                } else {
                    $tablesNotConfigured[ENTITY_SCOPE_ALL][$tableName] = $table;
                    $tablesNotConfigured[$table->getScope()][$tableName] = $table;
                }
            }

            foreach ($tablesNotConfigured as $_scope => $_tables) {
                $this->TABLES[$_scope] = array_merge($this->TABLES[$_scope], $_tables);
            }

            foreach ($tables as $tableName => $table) {
                $this->TABLES[ENTITY_SCOPE_ALL][$tableName] = $table;
                $this->TABLES[$table->getScope()][$tableName] = $table;
            }
        }

        return $this->TABLES[PsCheck::scope($scope)];
    }

    /** @return PsTable */
    public function getTable($table) {
        if ($table instanceof FoldedResources) {
            $table = $table->getTableName();
            //Если для фолдинга нет таблицы - возвращаем null
            return array_get_value($table, $this->getTables());
        }
        return check_condition(array_get_value($table, $this->getTables()), "Таблица [$table] не существует.");
    }

    private $MODIFIED_TABLES;

    public function getModifiedTables() {
        if (!is_array($this->MODIFIED_TABLES)) {
            $this->MODIFIED_TABLES = array();
            /* @var $table PsTable */
            foreach (PsTable::configured() as $name => $table) {
                if ($table->hasModified()) {
                    $this->MODIFIED_TABLES[$name] = $table;
                }
            }
        }
        return $this->MODIFIED_TABLES;
    }

    public function getModifiedTablesCount() {
        return count($this->getModifiedTables());
    }

    public function hasModifiedTables() {
        return $this->getModifiedTablesCount() > 0;
    }

    //TODO - выкинуть
    public function getTablesWithDependableCaches() {
        return AdminDbBean::inst()->getTablesWithDependableCaches();
    }

    /*
     * СОХРАНЕНИЕ/ИЗВЛЕЧЕНИЕ данных из таблиц
     */

    public function exportTableData($table) {
        $this->getTable($table)->exportDataToFile();
    }

    public function acceptDiff($table, $recIdent) {
        $this->getTable($table)->acceptDiff($recIdent);
    }

    public function acceptAllDiffs($table) {
        $this->getTable($table)->acceptAllDiffs();
    }

    /*
     * 
     * Синглтон
     * 
     */

    /** @return TableExporter */
    public static function inst() {
        return parent::inst();
    }

}

?>