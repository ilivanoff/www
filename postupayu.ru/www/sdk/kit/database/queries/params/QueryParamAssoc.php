<?php

/**
 * Ассоциативный параметр запроса: ключ=>значение.
 * 
 * @author azazello
 */
final class QueryParamAssoc implements QueryParam {

    const DEFAULT_OPERATOR = '=';
    const OPERATOR_IN = 'in';

    private $name;
    private $value;
    private $asBind;
    private $operator;
    private $extraBinds = array();

    function __construct($name, $value, $asBind = true, $operator = self::DEFAULT_OPERATOR, array $extraBinds = array()) {
        $this->name = PsCheck::tableColName($name);
        $this->value = $asBind ? PsCheck::queryBindParam($value, $name) : PsCheck::queryPlainExpression($value, $name);
        $this->asBind = $asBind;
        $this->operator = PsCheck::notEmptyString($operator);
        foreach ($extraBinds as $bindParam) {
            $this->extraBinds[] = PsCheck::queryBindParam($bindParam, $name);
        }
    }

    public function getName() {
        return $this->name;
    }

    public function getValue() {
        return $this->value;
    }

    public function getOperator() {
        return $this->operator;
    }

    public function isAsBind() {
        return $this->asBind;
    }

    public function getExtraBinds() {
        return $this->extraBinds;
    }

    public function __toString() {
        return __CLASS__ . " '$this->name $this->operator $this->value' " . ($this->extraBinds ? array_to_string($this->extraBinds) : '');
    }

}

?>