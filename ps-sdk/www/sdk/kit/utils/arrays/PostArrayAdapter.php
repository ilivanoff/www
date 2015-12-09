<?php

final class PostArrayAdapter extends ArrayAdapter {

    /** @return PostArrayAdapter */
    public static function inst() {
        return parent::inst($_POST, true);
    }

}

?>
