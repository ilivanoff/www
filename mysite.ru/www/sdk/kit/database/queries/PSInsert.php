<?php

/**
 * Description of PSInsert
 *
 * @author azazello
 */
final class PSInsert extends Query {

    private $table;

    function __construct($table, $what = null) {
        $this->table = PsCheck::tableName($table);
        $this->addWhat($what);
    }

    /** @return PSInsert */
    public function addWhat($what) {
        return parent::addWhat(self::assertOnlyAssocParams($what));
    }

    /** @return PSInsert */
    public function setWhat($column, $value) {
        return parent::setWhat($column, $value);
    }

    public function build(&$params = null) {
        $params = array();

        $query[] = 'insert into';
        $query[] = $this->table;
        $query[] = $this->fetchWhatInsCols();
        $query[] = 'values';
        $query[] = $this->fetchWhatInsVals($params);

        return concat($query);
    }

}

?>
