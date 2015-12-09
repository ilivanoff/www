<?php

class PsProfilerEmpty extends PsProfilerInterface {

    public function isEnabled() {
        return false;
    }

    public function getStats() {
        return array();
    }

}

?>