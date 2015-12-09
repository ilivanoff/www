<?php

/**
 * Типы настроек, хранимых в базе.
 */
final class DbPropType extends PsEnum {

    const COLUMN_N_VAL = 'n_val';
    const COLUMN_V_VAL = 'v_val';

    public static final function INT() {
        return self::inst(self::COLUMN_N_VAL, PsConst::PHP_TYPE_INTEGER);
    }

    public static final function STR() {
        return self::inst(self::COLUMN_V_VAL, PsConst::PHP_TYPE_STRING);
    }

    public static final function BOOL() {
        return self::inst(self::COLUMN_N_VAL, PsConst::PHP_TYPE_BOOLEAN);
    }

    private $colName;
    private $phpType;

    protected function init($column = null, $phpType = null) {
        $this->colName = PsCheck::tableColName($column);
        $this->phpType = PsCheck::phpType($phpType);
    }

    private function php2db($phpVal) {
        $type = PsCheck::phpType(gettype($phpVal), array($this->phpType, PsConst::PHP_TYPE_NULL));
        switch ($type) {
            case PsConst::PHP_TYPE_NULL:
                return null;
            case PsConst::PHP_TYPE_INTEGER:
                return $phpVal;
            case PsConst::PHP_TYPE_STRING:
                return trim($phpVal);
            case PsConst::PHP_TYPE_BOOLEAN:
                return $phpVal ? 1 : 0;
        }
        raise_error('Нет правил конвертации ' . __FUNCTION__ . ' для типа ' . $type);
    }

    private function db2php($dbVal) {
        if (is_null($dbVal)) {
            return null;
        }
        if (gettype($dbVal) == $this->phpType) {
            return $dbVal;
        }
        $dbVal = isTotallyEmpty($dbVal) ? null : trim($dbVal);
        switch ($this->phpType) {
            case PsConst::PHP_TYPE_INTEGER:
                return PsCheck::intOrNull($dbVal);
            case PsConst::PHP_TYPE_STRING:
                return $dbVal;
            case PsConst::PHP_TYPE_BOOLEAN:
                return !!$dbVal;
        }
        raise_error('Нет правил конвертации ' . __FUNCTION__ . ' для типа ' . $this->phpType);
    }

    public function validateDefault($default) {
        return PsCheck::phpVarType($default, array($this->phpType, PsConst::PHP_TYPE_NULL));
    }

    /**
     * Получение значения настройки
     */
    public function get(DbProp $prop) {
        return $this->db2php(UtilsBean::inst()->getDbProp($this->colName, $prop->name(), $prop->getDefault()));
    }

    /**
     * Установка нового значения настройки
     */
    public function set(DbProp $prop, $val) {
        UtilsBean::inst()->setDbProp($this->colName, $prop->name(), $this->php2db($val));
        return $val;
    }

}

?>