<?php

/*
 * Логгер логирует сразу в два файла - общий и для данного $id
 */

class PsLoggerImpl implements PsLoggerInterface {

    private $id;

    /** @var PsLogger */
    private $logger;

    function __construct($id, PsLogger $logger) {
        $this->id = $id;
        $this->logger = $logger;
    }

    public function infoBox($title, $msg = '', $marker = '+') {
        $len = ps_strlen($title);
        $line = pad_left('', $len + 4, $marker);
        $str = "\n$line\n$marker $title $marker\n$line\n$msg\n";
        $this->info($str);
    }

    public function info($msg = '', $param1 = null, $param2 = null, $param3 = null) {
        $num = func_num_args();
        if ($num > 1) {
            $params = func_get_args();
            unset($params[0]);
            $msg = PsStrings::replaceWithParams('{}', $msg, $params);
        }

        $this->logger->doWrite($this->id, $msg);
    }

    public function isEnabled() {
        return true;
    }

}

?>