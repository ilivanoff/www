<?php

/**
 * Базовый интерфейс для записи логов в файл/консоль и т.д.
 * 
 * @author azazello
 */
abstract class AbstractLogWriter {

    public abstract function initAndWriteFirstLog();

    public abstract function write($logId, $msg);

    public abstract function closeAndWriteFinalLog();

    public abstract function getFullLog();

    public static final function inst($type) {
        switch ($type) {
            case PsLogger::OUTPUT_FILE:
                return new FileLogWriter();
            case PsLogger::OUTPUT_CONSOLE:
                return new ConsoleLogWriter();
            case PsLogger::OUTPUT_BROWSER:
                return new BrowserLogWriter();
        }
        raise_error("Unknown log writer type: [$type].");
    }

}

?>
