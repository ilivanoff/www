<?php

/**
 * Description of PSUpdate
 *
 * @author azazello
 */
final class PSUpdate extends Query {

    private $table;

    function __construct($table, $what = null, $where = null) {
        $this->table = PsCheck::tableName($table);
        $this->addWhat($what);
        $this->addWhere($where);
    }

    /** @return PSUpdate */
    public function addWhat($what) {
        return parent::addWhat($what);
    }

    /** @return PSUpdate */
    public function setWhat($column, $value) {
        return parent::setWhat($column, $value);
    }

    /** @return PSUpdate */
    public function addWhere($where) {
        return parent::addWhere($where);
    }

    /** @return PSUpdate */
    public function setWhere($column, $value) {
        return parent::setWhere($column, $value);
    }

    public function build(&$params = null) {
        $params = array();

        $query[] = 'update';
        $query[] = $this->table;
        $query[] = 'set';
        $query[] = $this->fetchWhat($params);
        $query[] = $this->fetchWhere($params);

        return concat($query);
    }

}

?>
