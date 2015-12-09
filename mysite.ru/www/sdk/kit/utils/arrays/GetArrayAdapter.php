<?php

final class GetArrayAdapter extends ArrayAdapter {

    /** @return GetArrayAdapter */
    public static function inst() {
        return parent::inst($_GET, true);
    }

}

?>