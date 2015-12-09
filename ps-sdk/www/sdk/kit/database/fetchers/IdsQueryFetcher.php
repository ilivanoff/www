<?php

/**
 * Description of ObjectQueryFetcher
 *
 * @author azazello
 */
class IdsQueryFetcher extends ArrayQueryFetcher {

    public function fetchResult(array $ROWS) {
        $result = array();
        foreach ($ROWS as $row) {
            $id = (int) $row['id'];
            if ($this->filterValue($id)) {
                $result[] = $id;
            }
        }
        return $result;
    }

    /** @return IdsQueryFetcher */
    public static function inst() {
        return new IdsQueryFetcher(false, true);
    }

}

?>