<?php

final class UnloadArrayAdapter extends ArrayAdapter {

    /** @return UnloadArrayAdapter */
    public static function inst() {
        return parent::inst(array_get_value_unset(SESSION_UNLOAD_PARAMS, $_SESSION, array()));
    }

}

?>