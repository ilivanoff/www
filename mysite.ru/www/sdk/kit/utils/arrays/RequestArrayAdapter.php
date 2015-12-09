<?php

final class RequestArrayAdapter extends ArrayAdapter {

    /** @return RequestArrayAdapter */
    public static function inst() {
        return parent::inst($_REQUEST, true);
    }

}

?>
