<?php

/**
 * Класс содержит методы валидации различных параметров и вводимых данных
 *
 * @author azazello
 */
final class PsCheck {

    /**
     * Метод выбрасывает ошибку валидации, добавляя информацию о параметре к сообщению.
     */
    private static function raise($message, $var) {
        raise_error($message . '. Получено: [' . print_r($var, true) . '].');
    }

    /**
     * Проверка целочисленного значения
     */
    public static function isInt($var) {
        return is_inumeric($var);
    }

    /**
     * Проверка целочисленного значения
     */
    public static function int($var) {
        if (self::isInt($var)) {
            return (int) $var;
        }
        self::raise('Ожидается целочисленное значение', $var);
    }

    /**
     * Проверка целочисленного значения либо null
     */
    public static function intOrNull($var) {
        return is_null($var) ? null : self::int($var);
    }

    /**
     * Проверка области видимости сущности
     */
    public static function scope($scope) {
        if (in_array($scope, array(ENTITY_SCOPE_ALL, ENTITY_SCOPE_SDK, ENTITY_SCOPE_PROJ))) {
            return $scope;
        }
        self::raise('Ожидается валидный scope', $scope);
    }

    /**
     * Проверка значения на объект
     */
    public static function object($var) {
        if (is_object($var)) {
            return $var;
        }
        self::raise('Ожидается объект', $var);
    }

    /**
     * Проверка значения на объект
     */
    public static function arr($var) {
        if (is_array($var)) {
            return $var;
        }
        self::raise('Ожидается массив', $var);
    }

    /**
     * Проверка целочисленного значения
     */
    public static function positiveInt($var) {
        if (self::int($var) > 0) {
            return (int) $var;
        }
        self::raise('Ожидается положительное целочисленное значение', $var);
    }

    /**
     * Проверка строки на пустоту
     */
    public static function isNotEmptyString($var) {
        return is_string($var) && !isEmpty($var);
    }

    /**
     * Проверка строки на непустоту
     */
    public static function notEmptyString($var) {
        if (self::isNotEmptyString($var)) {
            return $var;
        }
        self::raise('Ожидается не пустая строка', $var);
    }

    /**
     * Проверка валидности названия столбца в таблице
     */
    private static function isValidTableName($tableName) {
        //Добавлен пробел на случай алиаса
        return self::isNotEmptyString($tableName) && (preg_match('/^[a-zA-Z_][a-zA-Z0-9_ ]*$/', $tableName) === 1);
    }

    /**
     * Проверка валидности названия таблицы
     */
    public static function tableName($tableName) {
        if (self::isValidTableName($tableName)) {
            return trim($tableName); //---
        }
        self::raise('Невалидное название таблицы', $tableName);
    }

    /**
     * Проверка валидности названия столбца в таблице
     */
    private static function isValidTableColName($colName) {
        return self::isNotEmptyString($colName) && (preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $colName) === 1);
    }

    /**
     * Проверка валидности названия столбца в таблице
     */
    public static function tableColName($colName) {
        if (self::isValidTableColName($colName)) {
            return trim($colName); //---
        }
        self::raise('Невалидное название столбца', $colName);
    }

    /**
     * Проверка валидности выражения, подставляемого в запрос 'как есть':
     * [id is not null] или [v_name in (?, ?)]
     */
    public static function isValidQueryPlainExpression($expression) {
        return self::isNotEmptyString($expression);
    }

    /**
     * Проверка валидности выражения, подставляемого в запрос 'как есть'
     */
    public static function queryPlainExpression($expression, $colName = null) {
        if (self::isValidQueryPlainExpression($expression)) {
            return $expression;
        }
        self::raise('Невалидное выражение для подстановки в запрос ' . ($colName ? "для '$colName'" : ''), $expression);
    }

    /**
     * Проверка валидности bind переменной для запроса (передаваемой через ?)
     */
    public static function queryBindParam($value, $colOrExpression) {
        if (!is_array($value) && !is_object($value)) {
            return $value;
        }
        self::raise('Невалидный тип bind-переменной ' . ($colOrExpression ? "для '$colOrExpression'" : ''), $value);
    }

    /**
     * Метод проверяет, является ли строка валидным email адресом
     */
    public static function isEmail($email) {
        return !!filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Метод проверяет, является ли строка валидным email адресом
     */
    public static function email($email) {
        if (self::isEmail($email)) {
            return $email;
        }
        self::raise('Ожидается валидный email адрес', $email);
    }

    /**
     * Метод проверяет тип переденной переменной
     * 
     * @param mixed $var
     * @param array $allowed - допустимые типы данных
     * @param array $denied - запрещённые типы данных
     * @return mixed
     */
    public static function phpType($type, array $allowed = null, array $denied = null) {
        if (!PsConst::hasPhpType($type)) {
            self::raise('Ожидается зарегистрированный тип данных php', $type);
        }
        if (!empty($allowed) && !in_array($type, $allowed)) {
            self::raise('Ожидается один из типов данных: ' . array_to_string($allowed), $type);
        }
        if (!empty($denied) && in_array($type, $denied)) {
            self::raise('Не ожидается один из типов данных: ' . array_to_string($denied), $type);
        }
        return $type;
    }

    /**
     * Метод проверяет тип переденной переменной
     * 
     * @param mixed $var
     * @param array $allowed - допустимые типы данных
     * @param array $denied - запрещённые типы данных
     * @return mixed
     */
    public static function phpVarType($var, array $allowed = null, array $denied = null) {
        self::phpType(gettype($var), $allowed, $denied);
        return $var;
    }

    /**
     * Функция проверяет, является ли переданная строка - строкой md5
     * http://stackoverflow.com/questions/14300696/check-if-string-is-an-md5-hash
     * Вариант с ctype_xdigit отбросим, так как большая буква в строке - это не md5.
     * return strlen($md5) == MD5_STR_LENGTH && (function_exists('ctype_xdigit') ? ctype_xdigit($md5) : preg_match('/^[a-f0-9]{32}$/', $md5));
     */
    public static function isMd5($md5 = '') {
        return strlen($md5) == MD5_STR_LENGTH && preg_match('/^[a-f0-9]{32}$/', $md5);
    }

}

?>