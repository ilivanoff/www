<?php

class PsCapture {

    public static function isValid() {
        $requestCapture = strtolower(array_get_value(FORM_PARAM_PSCAPTURE, $_REQUEST, ''));
        $sessionCapture = strtolower(array_get_value(FORM_PARAM_PSCAPTURE, $_SESSION, ''));
        return !isEmpty($sessionCapture) && ($sessionCapture === $requestCapture);
    }

    public static function reset() {
        unset($_SESSION[FORM_PARAM_PSCAPTURE]);
    }

}

?>