<?php

/**
 * Исключение на основе ошибки, выброшенной на основе trigger_error или подобным методом
 */
class PsErrorException extends Exception {

    public function __construct($message, $errorLevel = 0, $errorFile = '', $errorLine = 0) {
        parent::__construct($message, $errorLevel);
        $this->file = $errorFile;
        $this->line = $errorLine;
    }

}

?>
