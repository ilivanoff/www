<?php

/**
 * Description of ObjectQueryFetcher
 *
 * @author azazello
 */
class MapQueryFetcher extends ArrayQueryFetcher {

    public function fetchResult(array $ROWS) {
        $result = array();
        foreach ($ROWS as $row) {
            $id = $row['id']; //We cannot use key work in mysql:(
            $value = $row['value'];
            if ($this->filterKey($id) && $this->filterValue($value)) {
                $result[$id] = $value;
            }
        }
        return $result;
    }

    /** @return MapQueryFetcher */
    public static function inst() {
        return new MapQueryFetcher(true, true);
    }

}

?>