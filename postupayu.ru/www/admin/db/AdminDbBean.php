<?php

class AdminDbBean extends BaseBean {

    const CACHE_TABLES = 1;
    const CACHE_COLUMNS = 2;
    const CACHE_TABLES_CONFIGURED = 3;

    /**
     * Список таблиц текущей схемы
     */
    public function getTables() {
        if (!$this->getCache()->has(self::CACHE_TABLES)) {
            $this->getCache()->set(self::CACHE_TABLES, $this->getObjects('
SELECT LOWER(TABLE_NAME) as TABLE_NAME, 
       TABLE_COMMENT 
  FROM INFORMATION_SCHEMA.TABLES 
 WHERE table_schema=DATABASE() 
   and TABLE_TYPE=?', 'BASE TABLE', PsTable::getClass(), 'TABLE_NAME'));
        }
        return $this->getCache()->get(self::CACHE_TABLES);
    }

    /**
     * Таблица схемы
     * 
     * @return PsTable
     */
    public function getTable($name) {
        return check_condition(array_get_value($name, $this->getTables()), "Table $name is not exists");
    }

    /**
     * Метод проверяет существование таблицы с заданным названием
     * 
     * @param type $name
     */
    public function existsTable($name) {
        return array_key_exists($name, $this->getTables());
    }

    /**
     * Метод возвращает столбцы таблицы
     */
    public function getColumns($table) {
        if (!$this->getCache()->has(self::CACHE_COLUMNS)) {
            $this->getCache()->set(self::CACHE_COLUMNS, $this->getObjects("
select LOWER(c.TABLE_NAME) as TABLE_NAME,
       LOWER(c.COLUMN_NAME) as COLUMN_NAME,
       
       c.IS_NULLABLE,
       c.DATA_TYPE,
       c.CHARACTER_MAXIMUM_LENGTH,
       LOWER(c.COLUMN_KEY) as COLUMN_KEY,
       c.EXTRA,
       c.COLUMN_COMMENT,
       c.COLUMN_DEFAULT,
       
       if(upk.CONSTRAINT_TYPE is null, 0, 1) as IS_PK,
       
       if(ufk.CONSTRAINT_TYPE is null, 0, 1) as IS_FK,
       LOWER(ufk.REFERENCED_TABLE_NAME) as REFERENCED_TABLE_NAME,
       LOWER(ufk.REFERENCED_COLUMN_NAME) as REFERENCED_COLUMN_NAME

  from information_schema.columns c

  left join (select cpk.CONSTRAINT_TYPE,
                    upk.TABLE_SCHEMA,
                    upk.TABLE_NAME,
                    upk.COLUMN_NAME,
                    upk.TABLE_CATALOG
               from information_schema.KEY_COLUMN_USAGE  upk,
                    information_schema.TABLE_CONSTRAINTS cpk
              where upk.REFERENCED_TABLE_SCHEMA is null
                and upk.REFERENCED_TABLE_NAME is null
                and upk.REFERENCED_COLUMN_NAME is null
                and upk.TABLE_SCHEMA = cpk.TABLE_SCHEMA
                and upk.TABLE_NAME = cpk.TABLE_NAME
                and upk.CONSTRAINT_CATALOG = cpk.CONSTRAINT_CATALOG
                and upk.CONSTRAINT_NAME = cpk.CONSTRAINT_NAME
                and upk.CONSTRAINT_SCHEMA = cpk.CONSTRAINT_SCHEMA
                and cpk.CONSTRAINT_TYPE is not null
                and cpk.CONSTRAINT_TYPE = 'PRIMARY KEY') as upk
    on c.TABLE_SCHEMA = upk.TABLE_SCHEMA
   and c.TABLE_NAME = upk.TABLE_NAME
   and c.COLUMN_NAME = upk.COLUMN_NAME
   and c.TABLE_CATALOG = upk.TABLE_CATALOG

  left join (select LOWER(upk.REFERENCED_TABLE_NAME) as REFERENCED_TABLE_NAME,
                    LOWER(upk.REFERENCED_COLUMN_NAME) as REFERENCED_COLUMN_NAME,
                    
                    cpk.CONSTRAINT_TYPE,
                    upk.TABLE_SCHEMA,
                    upk.TABLE_NAME,
                    upk.COLUMN_NAME,
                    upk.TABLE_CATALOG,
                    upk.REFERENCED_TABLE_SCHEMA
               from information_schema.KEY_COLUMN_USAGE  upk,
                    information_schema.TABLE_CONSTRAINTS cpk
              where upk.REFERENCED_TABLE_SCHEMA is not null
                and upk.REFERENCED_TABLE_NAME is not null
                and upk.REFERENCED_COLUMN_NAME is not null
                and upk.TABLE_SCHEMA = cpk.TABLE_SCHEMA
                and upk.TABLE_NAME = cpk.TABLE_NAME
                and upk.CONSTRAINT_CATALOG = cpk.CONSTRAINT_CATALOG
                and upk.CONSTRAINT_NAME = cpk.CONSTRAINT_NAME
                and upk.CONSTRAINT_SCHEMA = cpk.CONSTRAINT_SCHEMA
                and cpk.CONSTRAINT_TYPE is not null
                and cpk.CONSTRAINT_TYPE = 'FOREIGN KEY') as ufk
    on c.TABLE_SCHEMA = ufk.TABLE_SCHEMA
   and c.TABLE_SCHEMA = ufk.REFERENCED_TABLE_SCHEMA
   and c.TABLE_NAME = ufk.TABLE_NAME
   and c.COLUMN_NAME = ufk.COLUMN_NAME
   and c.TABLE_CATALOG = ufk.TABLE_CATALOG

 where c.table_schema = DATABASE()
   /*and c.table_name = ?*/

 order by c.TABLE_NAME, c.ORDINAL_POSITION", null, PsTableColumn::getClass(), null, null, function(PsTableColumn $col, &$result, $row) {
                                $result[$col->getTableName()][$col->getName()] = $col;
                            }));
        }

        return array_get_value(lowertrim($table), $this->getCache()->get(self::CACHE_COLUMNS), array());
    }

    /**
     * Получает список зависимых групп кешей от данной таблицы
     */
    public function getTableDependableCaches($table) {
//Мы обратимся к маппингу, хранящему привязку ГруппаКешей -> СущностьБД
        $cacheMappingHash = Mappings::CACHE_DBENTITYS()->getHash();
        return $this->getValues('select distinct lident as value from ps_mappings where mhash=? and rident=?', array($cacheMappingHash, $table));
    }

    public function getTablesWithDependableCaches() {
//Мы обратимся к маппингу, хранящему привязку ГруппаКешей -> СущностьБД
        $cacheMappingHash = Mappings::CACHE_DBENTITYS()->getHash();
        return $this->getValues("select distinct rident as value from ps_mappings where mhash=? and rident not like 'v_%'", $cacheMappingHash);
    }

    /**
     * Загрузка списка триггеров таблицы
     */
    public function getTableTriggers($table) {
        return $this->getValues('select s.TRIGGER_NAME as value from INFORMATION_SCHEMA.TRIGGERS s where s.TRIGGER_SCHEMA = database() and s.event_object_schema = database() and s.event_object_table = ?', $table);
    }

    /**
     * Извлекает данные из таблицы для использования их в комбо-боксе.
     */
    public function getTableDataAsOptions($tableName, $pkColName, $valueColName) {
        return $this->getArrayIndexed("select $pkColName as value, concat('[',$pkColName,'] ',$valueColName) as content from $tableName", null, 'value');
    }

    /*
     * СИНГЛТОН
     */

    /** @return AdminDbBean */
    public static function inst() {
        return parent::inst();
    }

}

?>