<?php

/**
 * Description of PSDelete
 *
 * @author azazello
 */
final class PSDelete extends Query {

    private $table;

    function __construct($table, $where = null) {
        $this->table = PsCheck::tableName($table);
        $this->addWhere($where);
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

        $query[] = 'delete from';
        $query[] = $this->table;
        $query[] = $this->fetchWhere($params);

        return concat($query);
    }

}

?>
