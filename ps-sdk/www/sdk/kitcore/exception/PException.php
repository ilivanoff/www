<?php

/**
 * Основной класс исключения.
 *
 * @author Admin
 */
class PException extends Exception {

    public function __toString() {
        return "exception '" . __CLASS__ . "' with message '" . $this->getMessage() . "' in " . $this->getFile() . ":" . $this->getLine() . "\nStack trace:\n" . $this->getTraceAsString();
    }

}

?>