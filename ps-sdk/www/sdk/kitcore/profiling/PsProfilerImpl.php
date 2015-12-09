<?php

/**
 * Логгер логирует сразу в два файла - общий и для данного $id
 */
class PsProfilerImpl extends PsProfilerInterface {

    public function isEnabled() {
        return true;
    }

}

?>