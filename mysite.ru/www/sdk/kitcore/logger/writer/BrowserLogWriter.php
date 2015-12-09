<?php

class BrowserLogWriter extends AbstractLogWriter {

    /**
     * Полный лог
     */
    private $fullLog = '';

    public function initAndWriteFirstLog() {
        
    }

    public function write($logId, $msg) {
        $msg = "$logId: $msg";
        $this->fullLog.=$msg;
        echo $msg . '<br/>';
        return true;
    }

    public function closeAndWriteFinalLog() {
        
    }

    public function getFullLog() {
        return $this->fullLog;
    }

}

?>
