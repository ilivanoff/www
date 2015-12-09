<?php

interface PsLoggerInterface {

    public function info($msg = '', $param1 = null, $param2 = null, $param3 = null);

    public function infoBox($title, $msg = '', $marker = '+');

    public function isEnabled();
}

?>