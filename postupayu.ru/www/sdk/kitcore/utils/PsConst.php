<?php

/**
 * Константы системы
 *
 * @author azazello
 */
final class PsConst {
    /**
     * Расширения
     */

    const EXT_PHP = 'php';
    const EXT_CSS = 'css';
    const EXT_SQL = 'sql';
    const EXT_JS = 'js';
    const EXT_TPL = 'tpl';
    const EXT_ZIP = 'zip';
    const EXT_RAR = 'rar';
    const EXT_PROP = 'prop';
    const EXT_TXT = 'txt';
    const EXT_JPG = 'jpg';
    const EXT_JPEG = 'jpeg';
    const EXT_GIF = 'gif';
    const EXT_PNG = 'png';
    const EXT_MSGS = 'msgs';

    /**
     * Массив всех зарегистрированных расширений
     */
    public static function getExts() {
        return PsUtil::getClassConsts(__CLASS__, 'EXT_');
    }

    /**
     * Проверка расширения на существование
     */
    public static function hasExt($ext) {
        return in_array($ext, self::getExts());
    }

    /**
     * Типы данных php
     */

    const PHP_TYPE_BOOLEAN = 'boolean';
    const PHP_TYPE_INTEGER = 'integer';
    const PHP_TYPE_DOUBLE = 'double';
    const PHP_TYPE_FLOAT = 'float';
    const PHP_TYPE_STRING = 'string';
    const PHP_TYPE_ARRAY = 'array';
    const PHP_TYPE_OBJECT = 'object';
    const PHP_TYPE_RESOURCE = 'resource';
    const PHP_TYPE_NULL = 'NULL';
    const PHP_TYPE_UNKNOWN = 'unknown type';

    /**
     * Массив всех типов данных php
     */
    public static function getPhpTypes() {
        return PsUtil::getClassConsts(__CLASS__, 'PHP_TYPE_');
    }

    /**
     * Проверка типа данных php на существование
     */
    public static function hasPhpType($type) {
        return in_array($type, self::getPhpTypes());
    }

    /**
     * Макросы
     */

    const ID_REPLCASE_MACROS = '#id#';
    const NUM_REPLCASE_MACROS = '#num#';

}

?>
