<?php

/**
 * Класс занимается парсингом конфигов sdk и их переопределением.
 *
 * @author azazello
 */
final class DbIni extends AbstractIni {

    /**
     * Проверка, относится ли таблица к SDK
     */
    public static function isSdkTable($table) {
        return self::hasGroup($table, ENTITY_SCOPE_SDK);
    }

    /**
     * Возвращает названия всех таблиц, описанных в db.ini файлах
     */
    public static function getTables() {
        return self::getGroups();
    }

    /**
     * Возвращает названия всех таблиц SDK
     */
    public static function getSdkTables() {
        return self::getGroups(ENTITY_SCOPE_SDK);
    }

    /**
     * Возвращает названия всех сконфигурированных таблиц SDK
     */
    public static function getProjectTables() {
        return self::getGroups(ENTITY_SCOPE_PROJ);
    }

    /**
     * Метод проверяет, актуальна ли настройка для таблицы
     */
    public static function isTableHasPropertyCustom($tableName, $property, array $tableProperties) {
        return !isEmpty(PsCheck::phpVarType(array_get_value($property, $tableProperties), array(PsConst::PHP_TYPE_NULL, PsConst::PHP_TYPE_STRING)));
    }

    /**
     * Метод проверяет, актуальна ли настройка для столбца
     */
    public static function isTableHasProperty($tableName, $property) {
        return self::isTableHasPropertyCustom($tableName, $property, to_array(self::getGroupOrNull($tableName)));
    }

    /**
     * Метод возвращает колонки для указанной настройки
     */
    public static function getColumnsWithPropertyCustom($tableName, $property, array $tableProperties) {
        return to_array(PsCheck::phpVarType(array_get_value($property, $tableProperties), array(PsConst::PHP_TYPE_NULL, PsConst::PHP_TYPE_ARRAY)));
    }

    /**
     * Метод возвращает колонки для указанной настройки
     */
    public static function getColumnsWithProperty($tableName, $property) {
        return self::getColumnsWithPropertyCustom($tableName, $property, to_array(self::getGroupOrNull($tableName)));
    }

    /**
     * Метод проверяет, актуальна ли настройка для столбца
     */
    public static function isColumnHasPropertyCustom($tableName, $columnName, $property, array $tableProperties) {
        return in_array($columnName, self::getColumnsWithPropertyCustom($tableName, $property, $tableProperties));
    }

    /**
     * Метод проверяет, актуальна ли настройка для столбца
     */
    public static function isColumnHasProperty($tableName, $columnName, $property) {
        return self::isColumnHasPropertyCustom($tableName, $columnName, $property, to_array(self::getGroupOrNull($tableName)));
    }

}

?>