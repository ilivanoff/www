<?php

class FileLogWriter extends AbstractLogWriter {

    const SEPARATOR = '====================================';

    /**
     * Номер тукущей сессии логирования.
     * @var int
     */
    private $sessionNUM;

    /**
     * Менеджер директории логирования для текущей сессии.
     * @var DirManager
     */
    private $sessionDM;

    /**
     * Путь к общему файлу для логирования.
     * @var str
     */
    private $fileCommon;

    /**
     * Признак возможности записывать логи. Может стать false, если директории логирования будут удалены.
     */
    private $valid = true;

    /**
     * При инициализации:
     * 1. Получим из файла номер сессии
     * 2. Почистим/создадим директорию для логирования
     */
    public function initAndWriteFirstLog() {
        $DM = PsLogger::DM();

        $num = PsSequence::LOG()->next();
        $num = pad_left("$num", 2, '0');

        $logDirName = "session $num";
        $DM->cd(null, $logDirName)->clearDir();
        $fileCommon = $DM->absFilePath(null, '!session ' . $num, 'txt');

        //Сделаем первую запись
        $date = date(DF_PS);
        $this->doAppend($fileCommon, self::SEPARATOR . " $num. [$date] " . self::SEPARATOR);
        $this->doAppend($fileCommon, 'SERVER: ' . (isset($_SERVER) ? print_r($_SERVER, true) : ''));
        $this->doAppend($fileCommon, 'REQUEST: ' . (isset($_REQUEST) ? print_r($_REQUEST, true) : ''));
        $this->doAppend($fileCommon, 'SESSION: ' . (isset($_SESSION) ? print_r($_SESSION, true) : ''));
        $this->doAppend($fileCommon, 'FILES: ' . (isset($_FILES) ? print_r($_FILES, true) : ''));

        //Установим переменные класса
        $this->sessionDM = $DM;
        $this->sessionNUM = $num;
        $this->fileCommon = $fileCommon;
    }

    /**
     * Список файлов, соответствующих логгерам
     */
    private $FILES = array();

    public function write($logId, $msg) {
        if (!array_key_exists($logId, $this->FILES)) {
            //При поступлении первой записи от логгера, ему присваивается номер внутри сессии.
            $num = count($this->FILES) + 1;
            $num = pad_left("$num", 2, '0');
            $this->FILES[$logId] = array(
                'path' => $this->sessionDM->absFilePath(null, "$num. $logId", 'txt'),
                'trim' => true
            );
        }

        $doTrim = $this->FILES[$logId]['trim'];
        $logFilePath = $this->FILES[$logId]['path'];

        //Если мы делаем первую запись в файл, то сделаем trim(), чтобы логи не начинались с переносов строки
        if ($doTrim) {
            $msg = ltrim($msg);
            $doTrim = !$msg;
            $this->FILES[$logId]['trim'] = $doTrim;
        }

        /*
         * Начинаем запись. Стоит помнить, что файлы могли быть удалены во время работы.
         */
        $date = date(DF_PS);
        if (!$doTrim) {
            //Если мы не продолжаем делать трим, значит нашли первое не пустое сообщение и нужно писать лог.
            $this->doAppend($logFilePath, $msg);
        }
        $this->doAppend($this->fileCommon, "[$date] $logId: $msg");

        return $this->valid;
    }

    public function closeAndWriteFinalLog() {
        $date = date(DF_PS);
        $this->doAppend($this->fileCommon, self::SEPARATOR . " {$this->sessionNUM}. [$date] " . self::SEPARATOR);

        //Если номер сессии логирования всё ещё является последним - перенесём логи в папку lastsession
        if (PsSequence::LOG()->isCurrent($this->sessionNUM)) {
            $DM = PsLogger::DM('lastsession');
            $DM->clearDir();
            $files = $this->sessionDM->getDirContent(null, PsConst::EXT_TXT);
            /** @var DirItem */
            foreach ($files as $logDI) {
                $logDI->copyTo($DM->absFilePath(null, $logDI->getName()));
            }
        }
    }

    private function doAppend($absFilePath, $msg) {
        if ($this->valid) {
            $this->valid = file_append_contents($absFilePath, rtrim($msg) . "\n");
        }
    }

    public function getFullLog() {
        return '';
    }

}

?>
