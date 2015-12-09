<?php

/**
 * Настройки для столбца таблицы
 *
 * @author azazello
 */
final class PsTableColumnProps extends PsEnum {

    /** @return PsTableColumnProps */
    public static final function TABLE_CONFIGURED() {
        return self::inst('Сконфигурирована', 'Признак того, что таблица сконфигурирована и данные могут редактироваться через интерфейс');
    }

    /** @return PsTableColumnProps */
    public static final function TABLE_FILESYNC() {
        return self::inst('Синхронизируется с файлом', 'Данные таблицы будут синхронизированы с файлом');
    }

    /** @return PsTableColumnProps */
    public static final function COL_PK() {
        return self::inst('Замена ПК', 'Столбец заменяет первичный ключ, который является autoincrement полем');
    }

    /** @return PsTableColumnProps */
    public static final function COL_EXCLUDED() {
        return self::inst('Исключённые', 'Столбец не достепен при просмотре/создании/изменении');
    }

    /** @return PsTableColumnProps */
    public static final function COL_READONLY() {
        return self::inst('Только чтение', 'После создания записи данное поле уже нельзя будет поменять (как для полей, заменяющих ПК)');
    }

    private $shortText;
    private $longText;

    protected function init($shortText = null, $longText = null) {
        $this->shortText = $shortText;
        $this->longText = $longText;
    }

    public function getShortText() {
        return $this->shortText;
    }

    public function getLongText() {
        return $this->longText;
    }

    private function isTableProperty() {
        return starts_with($this->name(), 'TABLE_');
    }

    private function isColumnProperty() {
        return starts_with($this->name(), 'COL_');
    }

    /**
     * Метод проверяет, можно ли данное свойство устанавливать для таблицы
     */
    private function isAllowedForTable(PsTable $table) {
        return $this->isTableProperty();
    }

    /**
     * Метод проверяет, можно ли данное свойство устанавливать для столбцов таблицы
     */
    private function isAllowedForTableColumns(PsTable $table) {
        if ($this === self::COL_PK()) {
            //Замена ПК доступна, если перичного ключа нет или он - автоинкремент
            return !$table->hasPk() || $table->isPkAi();
        }
        return $this->isColumnProperty();
    }

    /**
     * Возвращает свойство по названию
     * 
     * @return PsTableColumnProps
     */
    public static function valueOf($name) {
        //TODO - вынести в PsEnum
        return $name instanceof PsTableColumnProps ? $name : parent::valueOf($name);
    }

    /**
     * Метод возвращает настройки, доступные для колонок таблицы
     */
    public static function getAllowedTableProperties(PsTable $table) {
        $tesult = array();
        /* @var $prop PsTableColumnProps */
        foreach (self::values() as $prop) {
            if ($prop->isAllowedForTable($table)) {
                $tesult[$prop->name()] = $prop;
            }
        }
        return $tesult; //----
    }

    /**
     * Метод возвращает настройки, доступные для колонок таблицы
     */
    public static function getAllowedColumnProperties(PsTable $table) {
        $tesult = array();
        /* @var $prop PsTableColumnProps */
        foreach (self::values() as $prop) {
            if ($prop->isAllowedForTableColumns($table)) {
                $tesult[$prop->name()] = $prop;
            }
        }
        return $tesult; //----
    }

    /**
     * Проверяет, обладает ли таблица данным свойством
     */
    public function isTableHasProperty($tableName) {
        return DbIni::isTableHasProperty($tableName, $this->name());
    }

    /**
     * Проверяет, обладает ли таблица данным свойством
     */
    public function isTableHasPropertyCustom($tableName, array $tableProperties) {
        return DbIni::isTableHasPropertyCustom($tableName, $this->name(), $tableProperties);
    }

    /**
     * Проверяет, обладает ли колонка данным свойством
     */
    public function isColumnHasProperty($tableName, $columnName) {
        return DbIni::isColumnHasProperty($tableName, $columnName, $this->name());
    }

    /**
     * Проверяет, обладает ли колонка данным свойством
     */
    public function isColumnHasPropertyCustom($tableName, $columnName, array $tableProperties) {
        return DbIni::isColumnHasPropertyCustom($tableName, $columnName, $this->name(), $tableProperties);
    }

    /**
     * Метод возвращает колонки для указанной настройки
     */
    public function getColumnsWithProperty($tableName) {
        return DbIni::getColumnsWithProperty($tableName, $this->name());
    }

    /**
     * Метод возвращает колонки для указанной настройки
     */
    public function getColumnsWithPropertyCustom($tableName, array $tableProperties) {
        return DbIni::getColumnsWithPropertyCustom($tableName, $this->name(), $tableProperties);
    }

    /**
     * Метод валидирует настройки таблицы, переданные извне
     */
    public static function validateTablePropertiesCustom(PsTable $table, array $tableProperties = null) {
        $errors = array();

        if ($tableProperties === null) {
            return $errors; // Нет настроет - нет проблем:)
        }

        $tableName = $table->getName();

        //Пробегаемся по настройкам, заданным для таблицы и валидируем её
        foreach ($tableProperties as $propName => $propValue) {
            if (!in_array($propName, self::names())) {
                $errors[] = "Настройка $propName для таблицы $tableName не существует.";
                continue; //---
            }

            $prop = self::valueOf($propName);

            /*
             * Таблица
             */
            if ($prop->isTableProperty()) {
                if (!$prop->isAllowedForTable($table)) {
                    $errors[] = "Настройка $propName не допустима для таблицы $tableName.";
                }
                continue; //---
            }

            /*
             * Столец
             */
            if ($prop->isColumnProperty()) {
                if (!$prop->isAllowedForTableColumns($table)) {
                    $errors[] = "Настройка $propName не допустима для столбцов таблицы $tableName.";
                    continue; //---
                }
                if (!is_array($propValue)) {
                    $errors[] = "Настройка $propName для таблицы $tableName должна быть задана в виде массива, передано: " . PsUtil::toString($propValue);
                    continue; //---
                }
                foreach ($propValue as $colName) {
                    if (!$table->hasColumn($colName)) {
                        $errors[] = "Настройка $propName для столбца $tableName.$colName некорректна - столбец не существует.";
                    }
                }
                continue; //---
            }

            raise_error("Invalid table col property [$propName]");
        }
        /*
         * Если мы обнаружили ошибку или таблица не сконфигурирована - возвращаем.
         * Мы не запрещаем, чтобы в db.ini были настройки для несконфигурированной таблицы,
         * но они должны быть корректно описаны.
         */
        if ($errors || !self::TABLE_CONFIGURED()->isTableHasPropertyCustom($tableName, $tableProperties)) {
            return $errors; //---
        }

        //Если нет первичного ключа или он является автоинкремент полем - нужен столбец, замещающий ПК
        if (!self::COL_PK()->getColumnsWithPropertyCustom($tableName, $tableProperties)) {
            //Нет альтернативы ПК
            if (!$table->hasPk()) {
                $errors[] = "Таблица $tableName не имеет первичного ключа и его альтернативы";
            } else if ($table->isPkAi()) {
                $errors[] = "Первичный ключ $tableName.{$table->getPk()->getName()} является autoincrement полем, и при этом не имеет альтернативы";
            }
        }

        return $errors;
    }

    /**
     * Метод валидирует настройки таблицы, заданные в b.ini
     */
    public static function validateTableProperties(PsTable $table) {
        return self::validateTablePropertiesCustom($table, DbIni::getGroupOrNull($table->getName()));
    }

}

?>