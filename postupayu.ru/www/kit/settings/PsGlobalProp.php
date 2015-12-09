<?php

/**
 * Класс-обёртка для глобальной настройки.
 * Глобальная настройка, это - свойство из Globals.php
 */
class PsGlobalProp {

    const TYPE_BOOLEAN = 'bool';
    const TYPE_NUMERIC = 'numeric';
    const TYPE_STRING = 'string';

    private $name;
    private $val;
    private $type;
    private $comment;
    private $info;

    public function __construct($name, $comment) {
        check_condition(defined($name), "Глобальное свойство [$name] не определено.");
        $this->name = $name;
        $this->val = constant($name);
        $this->comment = $comment;

        $type = gettype($this->val);
        //Определим тип
        switch ($type) {
            case 'boolean':
                $this->type = self::TYPE_BOOLEAN;
                break;
            case 'integer':
            case 'double':
            case 'float':
                $this->type = self::TYPE_NUMERIC;
                break;
            case 'string':
                $this->type = self::TYPE_STRING;
                break;
            default:
                /*
                 * "array"
                 * "object"
                 * "resource"
                 * "NULL"
                 * "unknown type"
                 */
                raise_error("Неизвестный тип [$type] для глобального свойства $name.");
                break;
        }

        $this->info = "{$this->name} ({$this->type})";
    }

    private function convert2type($value, $save4 = false) {
        check_condition(!is_null($value), "Передан null в качестве значения свойства {$this->name}");
        switch ($this->type) {
            case self::TYPE_BOOLEAN:
                $val = is_string($value) ? in_array($value, array('1', 'true')) : !!$value;
                return $save4 ? ($val ? 'true' : 'false') : $val;
            case self::TYPE_NUMERIC:
                check_condition(is_numeric($value), "Передано некорректное значение [$value] для свойства {$this->info}");
                $val = 1 * $value;
                return $save4 ? trim($val) : $val;
            case self::TYPE_STRING:
                $val = trim($value);
                return $save4 ? "'$val'" : $val;
        }
    }

    /**
     * Установка нового значения
     */
    private $dearty = false;
    private $newVal = null;

    public function setValue($value) {
        $this->newVal = $this->convert2type($value);
        $this->dearty = $this->newVal !== $this->val;
    }

    public function getValue() {
        return $this->dearty ? $this->newVal : $this->val;
    }

    public function getName() {
        return $this->name;
    }

    public function getComment() {
        return $this->comment;
    }

    public function isDearty() {
        return $this->dearty;
    }

    public function getEditType() {
        switch ($this->type) {
            case self::TYPE_BOOLEAN:
                return PsEditableGrid::TYPE_YN;
            case self::TYPE_NUMERIC:
                return PsEditableGrid::TYPE_NUMBER;
            case self::TYPE_STRING:
                return PsEditableGrid::TYPE_TEXT;
        }
    }

    public function getFileBlock() {
        $comment = "/*\n * " . $this->comment . "\n */";
        $name = $this->name;
        $strval = $this->convert2type($this->getValue(), true);
        $define = "define('$name', $strval);";

        return "$comment\n$define\n";
    }

    /**
     * Сбрасывает признак $dearty.
     * Вызывается после сохранения настройки в файл.
     */
    public function commit() {
        if ($this->dearty) {
            $this->val = $this->newVal;
            $this->newVal = null;
            $this->dearty = false;
        }
    }

    public function __toString() {
        return $this->info . ' ' . var_export($this->val, true);
    }

}

?>