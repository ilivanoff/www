<?php

class ConsoleLogWriter extends AbstractLogWriter {

    /**
     * Секундомером засечём общее время выполнения
     * 
     * @var Secundomer
     */
    private $secundomer;

    /**
     * Полный лог
     */
    private $fullLog;

    public function initAndWriteFirstLog() {
        $this->doWrite('Shell command execution started...');
        $this->secundomer = Secundomer::startedInst();
    }

    public function write($logId, $msg) {
        $this->doWrite($msg ? "$logId: $msg" : '');
        return true;
    }

    public function closeAndWriteFinalLog() {
        $this->secundomer->stop();
        $this->doWrite("Shell command execution finished in {$this->secundomer->getAverage()} seconds.");
    }

    private function doWrite($msg) {
        $msg = trim($msg);
        $msg = $msg ? mb_convert_encoding($msg, 'cp866', 'auto') : '';
        $msg = $msg ? '[' . date(DF_PS) . '] ' . $msg : '' . '';
        $msg = "$msg\n";

        echo $msg;
        $this->fullLog.=$msg;
    }

    public function getFullLog() {
        return $this->fullLog;
    }

}

?>
