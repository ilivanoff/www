<?php

/**
 * Класс, позволяющий строить запросы динамически.
 * 
 * Поддерживаются три основных вида запросов: select/update/insert.
 * В запросы можно добавлять два вида параметров:
 *  ассоциативные (ключ=>значение)
 *  текстовые (plain-параметры)
 * 
 * @author azazello
 */
abstract class Query {

    const MAX_IDS_CONCAT = 25;

    /*
     * Экземпляры
     */

    /** @return PSSelect */
    public static function select($what, $table, $where = null, $group = null, $order = null, $limit = null) {
        return new PSSelect($what, $table, $where, $group, $order, $limit);
    }

    /** @return PSUpdate */
    public static function update($table, $what = null, $where = null) {
        return new PSUpdate($table, $what, $where);
    }

    /** @return PSInsert */
    public static function insert($table, $what = null) {
        return new PSInsert($table, $what);
    }

    /** @return PSDelete */
    public static function delete($table, $where = null) {
        return new PSDelete($table, $where);
    }

    /**
     * Метод проверяет массив параметров, убеждаясь, что среди них есть только ассоциативные
     */
    public static function assertOnlyAssocParams($params) {
        if (is_array($params)) {
            foreach ($params as $key => $value) {
                if (is_inumeric($key)) {
                    self::assertOnlyAssocParams($value);
                }
            }
            return $params; //---
        }
        if (($params instanceof QueryParamPlain) || PsCheck::isValidQueryPlainExpression($params)) {
            raise_error('Запрос может содержать только ассоциативные параметры');
        }
        return $params; //---
    }

    /*
     * АССОЦИАТИВНЫЕ ПАРАМЕТРЫ
     */

    const ASSOC_PARAM_WHERE = 1;
    const ASSOC_PARAM_WHAT = 2;

    private $params = array();

    /** @return PSSelect */
    private function addParam($type, $param) {
        if (is_array($param)) {
            foreach ($param as $key => $value) {
                if (is_inumeric($key)) {
                    $this->addParam($type, $value);
                } else {
                    $this->setParam($type, $key, $value);
                }
            }
            return $this;
        }

        if ($param instanceof QueryParam) {
            return $this->registerQueryParam($type, $param);
        }

        if (PsCheck::isValidQueryPlainExpression($param)) {
            return $this->registerQueryParam($type, self::plainParam($param));
        }

        return $this;
    }

    /** @return PSSelect */
    private function setParam($type, $name, $value) {
        return $this->registerQueryParam($type, self::assocParam($name, $value));
    }

    /** @return PSSelect */
    private function registerQueryParam($type, QueryParam $param) {
        $this->params[$type][] = $param;
        return $this;
    }

    private function fetchAssocParams($type, $prefix, $glue, array &$params) {
        if (!array_key_exists($type, $this->params)) {
            return '';
        }
        $tokens = array();

        foreach ($this->params[$type] as $param) {

            //Ассоциаивные параметры
            if ($param instanceof QueryParamAssoc) {
                $name = $param->getName();
                $value = $param->getValue();
                $operator = $param->getOperator();
                $asBind = $param->isAsBind();

                $tokens[] = $name . ' ' . $operator . ' ' . ($asBind ? '?' : "$value");
                if ($asBind) {
                    $params[] = $value;
                }
                foreach ($param->getExtraBinds() as $bindParam) {
                    $params[] = $bindParam;
                }
                continue; //---
            }

            //Plain параметры
            if ($param instanceof QueryParamPlain) {
                $tokens[] = $param->getExpression();
                foreach ($param->getBindParams() as $bindParam) {
                    $params[] = $bindParam;
                }
                continue; //---
            }

            raise_error('Неизвестный тип параметра запроса: ' . print_r($param));
        }

        return self::concatQueryTokens($prefix, $tokens, $glue);
    }

    /**
     * Метод объединяет элементы массива в строку для запроса
     */
    protected static function concatQueryTokens($prefix, $tokens, $glue = ', ', $takeTotallyEmpty = false) {
        $paramsStr = trim(is_array($tokens) ? concat($tokens, $glue, $takeTotallyEmpty) : $tokens);
        return $paramsStr ? trim("$prefix $paramsStr") : '';
    }

    /*
     * 
     */

    protected function addWhere($where) {
        return $this->addParam(self::ASSOC_PARAM_WHERE, $where);
    }

    protected function setWhere($column, $value) {
        return $this->setParam(self::ASSOC_PARAM_WHERE, $column, $value);
    }

    protected function fetchWhere(array &$params) {
        return $this->fetchAssocParams(self::ASSOC_PARAM_WHERE, 'where', ' and ', $params);
    }

    protected function addWhat($what) {
        return $this->addParam(self::ASSOC_PARAM_WHAT, $what);
    }

    protected function setWhat($column, $value) {
        return $this->setParam(self::ASSOC_PARAM_WHAT, $column, $value);
    }

    protected function fetchWhat(array &$params) {
        return $this->fetchAssocParams(self::ASSOC_PARAM_WHAT, '', ', ', $params);
    }

    private function getInsertParams() {
        return self::assertOnlyAssocParams(array_get_value(self::ASSOC_PARAM_WHAT, $this->params, array()));
    }

    protected function fetchWhatInsCols() {
        $columns = array();
        foreach ($this->getInsertParams() as $param) {
            $columns[] = $param->getName();
        }
        $cols = self::concatQueryTokens('', $columns);
        return '(' . check_condition($cols, 'Не указан список колонок для вставки') . ')';
    }

    protected function fetchWhatInsVals(array &$params) {
        $tokens = array();
        foreach ($this->getInsertParams() as $param) {
            $value = $param->getValue();
            $asBind = $param->isAsBind();

            if ($asBind) {
                $tokens[] = '?';
                $params[] = $value;
            } else {
                $tokens[] = $value;
            }
        }
        return '(' . check_condition(self::concatQueryTokens('', $tokens, ', ', true), 'Не указаны данные для вставки') . ')';
    }

    public abstract function build(&$params = null);

    public final function __toString() {
        return trim($this->build($params)) . ($params ? ' ' . array_to_string($params) : '');
    }

    /**
     * Ассоциативный параметр запроса: ключ->значение
     * 
     * @param str $name - название параметра
     * @param mixed $value - значение параметра
     * @param bool $asBind - признак передачи параметра через bind (как ?, 'id=?') или напрямую в запрос (dt_event=unix_timestamp())
     * @param str $operator - оператор для параметра: 'id=?' или 'n_order>3'
     * @param array $extraBinds - дополнительные bind-переменные. Например для выражений in.
     * @return QueryParamAssoc
     */
    public static function assocParam($name, $value, $asBind = true, $operator = QueryParamAssoc::DEFAULT_OPERATOR, array $extraBinds = array()) {
        return new QueryParamAssoc($name, $value, $asBind, $operator, $extraBinds);
    }

    /**
     * Текстовый параметр запроса
     * 
     * @param str $expression  - выражение 'id_user is not null' или 'n_type in (?, ?)'
     * @param array $bindParams - bind переменные для запроса
     * @return QueryParamPlain
     */
    public static function plainParam($expression, array $bindParams = array()) {
        return new QueryParamPlain($expression, $bindParams);
    }

    /**
     * Ассоциативный параметр запроса для подстановки вида id in (...)
     * 
     * @param str $name - название параметра
     * @param array $values - массив значений
     * @param type $limit - размер порции
     * @return array
     */
    public static function assocParamsIn($name, array $values, $limit = self::MAX_IDS_CONCAT) {
        $limit = PsCheck::positiveInt($limit);
        $values = array_unique($values);
        sort($values);
        $taken = array();
        $params = array();
        foreach ($values as $value) {
            //$taken[] = PsCheck::int($value);
            $taken[] = $value;
            $count = count($taken);
            if ($count >= $limit) {
                switch ($count) {
                    case 1:
                        $params[] = self::assocParam($name, $taken[0]);
                        break;
                    default:
                        $params[] = self::assocParam($name, '(' . implode(',', array_fill(0, $count, '?')) . ')', false, QueryParamAssoc::OPERATOR_IN, $taken);
                        break;
                }
                $taken = array();
            }
        }

        $count = count($taken);
        switch ($count) {
            case 0:
                break;
            case 1:
                $params[] = self::assocParam($name, $taken[0]);
                break;
            default:
                $params[] = self::assocParam($name, '(' . implode(',', array_fill(0, $count, '?')) . ')', false, QueryParamAssoc::OPERATOR_IN, $taken);
                break;
        }
        return $params;
    }

}

?>