<?php

/**
 * Description of ObjectQueryFetcher
 *
 * @author azazello
 */
class ValuesQueryFetcher extends ArrayQueryFetcher {

    public function fetchResult(array $ROWS) {
        $result = array();
        foreach ($ROWS as $row) {
            $value = $row['value'];
            if ($this->filterValue($value)) {
                $result[] = $value;
            }
        }
        return $result;
    }

    /** @return ValuesQueryFetcher */
    public static function inst() {
        return new ValuesQueryFetcher(false, true);
    }

}

?>