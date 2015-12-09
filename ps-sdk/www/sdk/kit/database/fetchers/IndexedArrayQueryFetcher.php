<?php

/**
 * Description of ObjectQueryFetcher
 *
 * @author azazello
 */
class IndexedArrayQueryFetcher extends ArrayQueryFetcher {

    /** Колонка индексирования */
    private $idxCol;

    protected function __construct($idxCol) {
        parent::__construct(true, false);
        $this->idxCol = $idxCol;
    }

    public function fetchResult(array $ROWS) {
        $result = array();
        foreach ($ROWS as $row) {
            $idx = $row[$this->idxCol];
            if ($this->filterKey($idx)) {
                $result[$idx] = $row;
            }
        }
        return $result;
    }

    /** @return IndexedArrayQueryFetcher */
    public static function inst($idxCol) {
        return new IndexedArrayQueryFetcher($idxCol);
    }

}

?>