<?php

final class PsProfiler implements Destructable {

    /** @var SimpleDataCache */
    private $CAHCE;

    /**
     * Признак включённости профилирования.
     * Вычисляется единожды при запуске и больше не переустанавливается - если начали профилировать сессию,
     * то профилируем её до конца.
     */
    private $enabled;

    /** @var DirManager */
    private $dirManager;

    /*
     * Класс является фабрикой логгеров
     */
    private static $inst;

    /** @return PsProfiler */
    public static function controller() {
        return isset(self::$inst) ? self::$inst : self::$inst = new PsProfiler();
    }

    //Экземпляр профайлера
    /** @return PsProfilerInterface */
    public static function inst($id = __CLASS__) {
        return self::controller()->getProfiler($id);
    }

    private function __construct() {
        $this->CAHCE = new SimpleDataCache();
        $this->enabled = PsDefines::isProfilingEnabled();
        $this->dirManager = DirManager::autogen('profilers');
        if ($this->enabled) {
            PsShotdownSdk::registerDestructable($this, PsShotdownSdk::PsProfiler);
        }
    }

    /** @return DirItem Файл для хранения статистики */
    private function getProfilerDi($profilerId) {
        return $this->dirManager->getDirItem(null, $profilerId, 'txt');
    }

    /** @return PsProfilerInterface */
    private function getProfiler($profilerId) {
        //Не будем выполнять get_file_name($profilerId), так как нужно вернуть профайлер максимально быстро
        if ($this->CAHCE->has($profilerId)) {
            return $this->CAHCE->get($profilerId);
        }

        check_condition($profilerId, 'Profiler id cannot be empty.');

        if (!$this->enabled) {
            return $this->CAHCE->set($profilerId, new PsProfilerEmpty($profilerId));
        }

        $di = $this->getProfilerDi($profilerId);
        $pr = null;

        //Проверим текущий размер профайлера
        if ($di->isMaxSize(PROFILING_MAX_FILE_SIZE)) {
            $locked = PsLock::lock(__CLASS__ . "::compressProfiler($profilerId)", false);
            if ($locked) {
                $this->compressProfiler($di);
                PsLock::unlock();
            } else {
                //Разимер превышен и мы не смогли получить лок для дампа профайлера. Не будем в этот раз вести профайлинг.
                $pr = new PsProfilerEmpty($profilerId);
            }
        }

        return $this->CAHCE->set($profilerId, $pr ? $pr : new PsProfilerImpl($profilerId));
    }

    /**
     * Деструктор.
     * Именно в нём мы сохраним данные всех профайлеров в их файлы.
     * В этот метод мы попадём, только если профайлинг активен.
     */
    public function onDestruct() {
        /*
         * Получим профайлер - счётчик времени выполнения скрипта.
         * Мы должны его получить именно сейчас, так как кто-то другой мог им пользоваться раньше
         * и в нём уже могут быть данные.
         */
        $commonProfiler = $this->getProfiler(__CLASS__);

        /* @var $profiler PsProfilerInterface */
        foreach ($this->CAHCE->data as $profilerId => $profiler) {
            if ($profilerId != __CLASS__) {
                $this->saveProfiler2File($profilerId, $profiler);
            }
        }

        $commonProfiler->add('ScriptExecution', Secundomer::inst()->add(1, microtime(true) - SCRIPT_EXECUTION_START));
        $this->saveProfiler2File(__CLASS__, $commonProfiler);
    }

    /**
     * Сохраняет статистику, собранную профайлером, в файл.
     */
    private function saveProfiler2File($profilerId, PsProfilerInterface $profiler) {
        if (!$profiler->isEnabled()) {
            return; //---
        }

        $saved = $this->saveToFile($this->getProfilerDi($profilerId), $profiler->getStats());

        //TODO! В данный момент можно делать $profiler->reset(), но мы делаем это в onDestruct, поэтому не будем проверять.

        if (PsLogger::isEnabled() && $saved) {
            PsLogger::inst(__CLASS__)->info("+ $profilerId");
            PsLogger::inst(__CLASS__)->info($saved);
        }
    }

    /**
     * Преобразует коллекцию секундомеров в строрку, пригодную для сохранения в файл, и записывает её
     */
    private function saveToFile(DirItem $item, array $secundomers, $rewrite = false) {
        $string = '';
        /* @var $secundomer Secundomer */
        foreach ($secundomers as $ident => $secundomer) {
            //Обязательно нужно удалить переносы строк из идентификатора, чтобы наш файл "не поехал"
            $ident = normalize_string($ident);

            if (!$ident || $secundomer->isStarted()) {
                continue;
            }

            $string .= $ident . '|' . $secundomer->getCount() . '|' . $secundomer->getTotalTime() . "\n";
        }

        $item->writeToFile($string, $rewrite);

        return $string;
    }

    /*
     * == CONTROLLER ONLY ==
     */

    public function resetAll() {
        check_condition(!PROFILING_ENABLED, 'Cannot clear profilers when profiling is on.');
        $this->dirManager->clearDir();
    }

    /**
     * Собирает статистику по переданному профайлеру или по всем профайлерам сразу.
     * Достигается это путём разбора файла, относящегося к профайлеру.
     */
    public function getStats($profilerId = null) {
        $result = array();
        $files = $profilerId ? $this->getProfilerDi($profilerId) : $this->dirManager->getDirContent(null, PsConst::EXT_TXT);
        /** @var DirItem */
        foreach (to_array($files) as $file) {
            $result[$file->getNameNoExt()] = $this->parseProfiler($file);
        }
        return $result;
    }

    /**
     * Парсит файл профайлинга и возвращает массив вида:
     * ident=>Secundomer
     * Идентификатор здесь, это идентификатор профилируемой сущности, например - текст запроса.
     */
    private function parseProfiler(DirItem $file) {
        $result = array();

        $lines = $file->getFileLines(false);
        if (empty($lines)) {
            return $result; //---
        }

        foreach ($lines as $line) {
            $tokens = explode('|', $line);

            if (count($tokens) != 3) {
                continue;
            }

            $ident = trim($tokens[0]);
            $count = trim($tokens[1]);
            $time = trim($tokens[2]);

            if (!$ident || !is_numeric($count) || !is_numeric($time)) {
                continue;
            }

            if (!array_key_exists($ident, $result)) {
                $result[$ident] = Secundomer::inst();
            }
            $result[$ident]->add($count, $time);
        }
        return $result;
    }

    /**
     * Метод "ужимает" профайлер, а именно - соберает построчно всё его содержимое 
     * и записывает в файл данного профайлера.
     * 
     * @param DirItem $file - файл профайлера
     */
    private function compressProfiler(DirItem $file) {
        $this->saveToFile($file, $this->parseProfiler($file), true);
    }

    /**
     * Включение/отключение профилирования
     */
    public function setProfilingEnabled($isEnabled) {
        PsGlobals::inst()->getProp('PROFILING_ENABLED')->setValue($isEnabled);
        PsGlobals::inst()->save2file();
    }

}

?>
