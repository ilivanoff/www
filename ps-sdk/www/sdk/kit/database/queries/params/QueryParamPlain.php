<?php

/**
 * Параметр запроса.
 * 
 * @author azazello
 */
final class QueryParamPlain implements QueryParam {

    private $expression;
    private $bindParams = array();

    function __construct($expression, array $bindParams = array()) {
        $this->expression = PsCheck::queryPlainExpression($expression);
        $this->addBindParams($bindParams);
    }

    public function getExpression() {
        return $this->expression;
    }

    public function getBindParams() {
        return $this->bindParams;
    }

    /** @return QueryParamPlain */
    public function addBindParam($param) {
        $this->bindParams[] = PsCheck::queryBindParam($param, $this->expression);
        return $this;
    }

    /** @return QueryParamPlain */
    public function addBindParams(array $params) {
        foreach ($params as $param) {
            $this->addBindParam($param);
        }
        return $this;
    }

    public function __toString() {
        return __CLASS__ . ' [expression=' . $this->expression . ', bindParams=' . array_to_string($this->bindParams) . ']';
    }

}

?>