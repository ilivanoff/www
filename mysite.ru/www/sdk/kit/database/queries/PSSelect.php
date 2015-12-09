<?php

/**
 * Description of PSSelect
 *
 * @author azazello
 */
final class PSSelect extends Query {

    private $what;
    private $table;
    private $group;
    private $order;
    private $limit;

    function __construct($what, $table, $where = null, $group = null, $order = null, $limit = null) {
        $this->table = PsCheck::tableName($table);
        $this->what = PsCheck::notEmptyString(self::concatQueryTokens('', $what));
        $this->addWhere($where)->setGroup($group)->setOrder($order)->setLimit($limit);
    }

    /** @return PSSelect */
    public function addWhere($where) {
        return parent::addWhere($where);
    }

    /** @return PSSelect */
    public function setWhere($column, $value) {
        return parent::setWhere($column, $value);
    }

    /** @return PSSelect */
    public function setGroup($group) {
        $this->group = $group;
        return $this;
    }

    /** @return PSSelect */
    public function setOrder($order) {
        $this->order = $order;
        return $this;
    }

    /** @return PSSelect */
    public function setLimit($limit) {
        if (is_array($limit)) {
            $this->limit = $limit;
        } else {
            $this->limit = PsCheck::intOrNull($limit);
            $this->limit = is_null($this->limit) || ($this->limit <= 0) ? null : $this->limit;
        }
        return $this;
    }

    public function build(&$params = null) {
        $params = array();

        $query[] = 'select';
        $query[] = $this->what;
        $query[] = 'from';
        $query[] = $this->table;
        $query[] = $this->fetchWhere($params);
        $query[] = self::concatQueryTokens('group by', $this->group);
        $query[] = self::concatQueryTokens('order by', $this->order);
        $query[] = self::concatQueryTokens('limit', $this->limit);

        return concat($query);
    }

}

?>
