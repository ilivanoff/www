<?php

class PsLoggerEmpty implements PsLoggerInterface {

    public function info($msg = '', $param1 = null, $param2 = null, $param3 = null) {
        //Do nothing
    }

    public function infoBox($title, $msg = '', $marker = '+') {
        //Do nothing
    }

    public function isEnabled() {
        return false;
    }

}

?>
