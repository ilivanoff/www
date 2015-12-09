<?php

final class PsLogger implements Destructable {
    //Моксимальное количество логируемых сессий

    const MAX_SESSIONS = 100;

    /**
     * Варианты вывода
     */
    const OUTPUT_FILE = 1;
    const OUTPUT_CONSOLE = 2;
    const OUTPUT_BROWSER = 3;

    /**
     * Сохранённые экземпляры логгеров.
     * @var $CACHE SimpleDataCache
     */
    private $CACHE;

    /**
     * Признак влючённости логирования. 
     * Вычисляется единожды при старте и может измениться только в одном направлении: вкл->выкл
     * при удалении директории логирования.
     * 
     * Мы не будем перевычислять этот признак по глобальным настройкам: если уж мы начали логировать сессию,
     * то не будем обрывать лог, так как потом не сможем понять - лог так и должен завершаться или это мы его прервали.
     * @var bool
     */
    private $enabled;

    /**
     * Пустой логгер, возвращается в случае отключённого логирования.
     * @var PsLoggerEmpty
     */
    private $emptyLogger;

    /**
     * Менеджер непосредстенной записи логов
     *
     * @var AbstractLogWriter
     */
    private $writer;

    /*
     * Класс является фабрикой логгеров
     */
    private static $inst;

    /**
     * Контроллер, который может выполняет все действия с логами.
     * Вызывается, восновном, из админского контекста.
     * 
     * @return PsLogger
     */
    public static function controller() {
        return isset(self::$inst) ? self::$inst : self::$inst = new PsLogger();
    }

    //Экземпляр логгера
    /** @return PsLoggerInterface */
    public static function inst($logId = 'log') {
        return self::controller()->getLogger($logId);
    }

    //Признак включённости
    public static function isEnabled() {
        return self::controller()->enabled;
    }

    private function __construct() {
        $this->CACHE = new SimpleDataCache();
        $this->emptyLogger = new PsLoggerEmpty();

        /*
         * Вычислим признак включённости логирования.
         */
        $this->enabled = PsDefines::isLoggingEnabled();
        if ($this->enabled) {
            $this->writer = AbstractLogWriter::inst(PsDefines::getLoggingStream(self::OUTPUT_FILE));
            $this->writer->initAndWriteFirstLog();
            PsShotdownSdk::registerDestructable($this, PsShotdownSdk::PsLogger);
        }
    }

    /** @return DirManager */
    public static function DM($dirs = null) {
        return DirManager::autogen(array('logs', $dirs));
    }

    /** @return PsLoggerInterface */
    private function getLogger($logId) {
        if (!$this->isLoggerCanWrite($logId, false)) {
            return $this->emptyLogger;
        }

        $logId = get_file_name($logId);

        if ($this->CACHE->has($logId)) {
            return $this->CACHE->get($logId);
        }

        return $this->CACHE->set($logId, new PsLoggerImpl($logId, $this));
    }

    /**
     * Включённые логгеры. Мы можем определить список конкретных логгеров, которые должны работать.
     * Все общие сообщения (начало работы, окончание работы и т.д.) - пишутся от имени данного класса.
     * 
     * Если null - включены все логгеры. Список работающих логгеров может измениться в результате модификации
     * глобального свойства с соответствующим названием.
     */
    private function isLoggerCanWrite($logId = __CLASS__, $checkExists = true) {
        if ($this->enabled) {
            if ($checkExists && ($logId != __CLASS__)) {
                //Если мы пишем не от имени текущего класса, то первоначально проверим - а есть ли такой логгер вообще
                check_condition($this->CACHE->has($logId), "Logger [$logId] is not registered.");
            }
            //Мог быть установлен список логгеров для данной сессии, проверим
            $loggers = PsDefines::getLoggersList();
            return !is_array($loggers) || in_array($logId, $loggers);
        }
        return false;
    }

    /**
     * Метод вызывается логгерами для записи строки во врайтер логов.
     */
    public function doWrite($logId, $msg) {
        if ($this->isLoggerCanWrite($logId)) {
            $this->enabled = $this->writer->write($logId, $msg);
        }
    }

    /**
     * Завершение работы. Мы можем записать последнюю строку в общий файл сессии.
     */
    public function onDestruct() {
        if ($this->isLoggerCanWrite()) {
            $this->writer->closeAndWriteFinalLog();
        }
    }

    /**
     * Метод возвращает полный лог этой сессии.
     */
    public function getFullLog() {
        return $this->writer ? $this->writer->getFullLog() : '';
    }

    /*
     * == CONTROLLER ONLY ==
     */

    /**
     * Возвращает список всех директорий лога
     */
    public function getLogDirs() {
        return $this->DM()->getDirContent(null, DirItemFilter::DIRS);
    }

    /**
     * Возвращает списко файлов заданной директории
     */
    public function getLogFiles($dir) {
        return $this->DM()->getDirContent($dir, PsConst::EXT_TXT);
    }

    /**
     * Возвращает конкретный лог-файл
     * @return DirItem
     */
    public function getLogFile($dir, $file) {
        return $this->DM()->getDirItem($dir, $file, 'txt');
    }

    /**
     * Возвращает текущий номер сессии (может быть и не установлен)
     */
    public function getLastSessionId() {
        return $this->DM()->getDirItem(null, 'lastnum')->getFileContents(false);
    }

    /**
     * Очистка всех логов
     */
    public function clearLogs() {
        check_condition(!LOGGING_ENABLED, 'Cannot clear logs when logging is on.');
        $this->DM()->clearDir();
    }

    /**
     * Включение/отключение логирования
     */
    public function setLoggingEnabled($isEnabled) {
        PsGlobals::inst()->getProp('LOGGING_ENABLED')->setValue($isEnabled);
        PsGlobals::inst()->save2file();
    }

}

?>
