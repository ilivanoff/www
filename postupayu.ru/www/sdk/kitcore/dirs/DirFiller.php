<?php

final class DirFiller {

    /** @var PsLoggerInterface */
    private $LOGGER;

    /** @var PsProfilerInterface */
    private $PROFILER;

    /* Обрабатываемый путь */
    private $PATH;

    /* Исключённые директории */
    private $EXCLUDED = array();

    /* Форсированно включённые директории */
    private $INCLUDED = array();

    /* Список директорий, содержимое которых можно взять */
    private $DIRS = array();

    /* Список директорий, содержимое которых будет пропущено */
    private $DIRSEXCLUDED = array();

    /**
     * Метод рекурсивно загружает содержимое указанной директории
     * 
     * @param type $absDirPath - директория
     * @param type $filterName - фильтр
     * @param type $excluded   - исключённые директории, анализ элементов в которой производиться не будет
     * @param type $included   - включённые директории, которые будут форсированно обработаны
     * @return array
     */
    public static function fill($absDirPath, $filterName, array $excluded = array(), array $included = array()) {
        return is_dir($absDirPath) ? (new DirFiller($absDirPath, $excluded, $included))->doFill($filterName) : array();
    }

    /**
     * Конструктор. В нём мы составим полный список директорий, в которых потом будет производиться поиск
     * 
     * @param type $absDirPath
     * @param array $excluded
     * @param array $included
     */
    private function __construct($absDirPath, array $excluded, array $included) {
        $this->LOGGER = PsLogger::inst(__CLASS__);
        $this->PROFILER = PsProfiler::inst(__CLASS__);

        $this->PATH = ensure_dir_endswith_dir_separator($absDirPath);
        $this->LOGGER->info("Dir: [{$this->PATH}]");

        foreach ($excluded as $dir) {
            $path = ensure_dir_endswith_dir_separator(array($absDirPath, $dir));
            $this->EXCLUDED[] = $path;
            $this->LOGGER->info("ExcludeDir: [$path]");
        }

        foreach ($included as $dir) {
            $path = ensure_dir_endswith_dir_separator(array($absDirPath, $dir));
            $this->INCLUDED[] = $path;
            $this->LOGGER->info("IncludeDir: [$path]");
        }

        $this->LOGGER->info('Filling dirs for search');
        $this->PROFILER->start('Fill search dirs');
        $this->fillWithDirs($this->PATH);
        $this->LOGGER->info('Total dirs for search count: {}, excluded dirs: {}. Taken time: {}sec.', count($this->DIRS), count($this->DIRSEXCLUDED), $this->PROFILER->stop()->getTime());
        foreach ($this->DIRSEXCLUDED as $path) {
            $this->LOGGER->info("- $path");
        }
    }

    private function isPathExcluded($path) {
        return in_array($path, $this->EXCLUDED) && !in_array($path, $this->INCLUDED);
    }

    private function getPathForceIncluded($path) {
        $res = array();
        foreach ($this->INCLUDED as $incpath) {
            if (contains_substring($incpath, $path)) {
                $res[] = $incpath;
            }
        }
        return $res;
    }

    private function fillWithDirs($path) {
        if (is_dir($path)) {
            $path = ensure_dir_endswith_dir_separator($path);
            if ($this->isPathExcluded($path)) {
                $this->DIRSEXCLUDED[] = $path;
                foreach ($this->getPathForceIncluded($path) as $subPath) {
                    $this->fillWithDirs($subPath);
                }
            } else {
                $this->LOGGER->info("+ $path");
                $this->DIRS[] = $path;
                $subDirNames = DirManager::inst($path)->getSubDirNames();
                foreach ($subDirNames as $name) {
                    $this->fillWithDirs($path . $name);
                }
            }
        }
    }

    /**
     * Непосредственное наполнение
     */
    private function doFill($filterName = null) {
        $this->LOGGER->info("Loading full content of dir [{}] with [{}] filter.", $this->PATH, PsUtil::toString($filterName));
        $this->PROFILER->start('doFill');

        /*
         * На данный момент у нас все директории собраны, остаётся только пробежаться по ним
         * и взять всё необходимое.
         * При этом нужно выполнить array_values, так как getDirContent возвращает ассоциативный массив,
         * а файлы в директориях могут повторяться.
         */
        $RESULT = array();
        foreach ($this->DIRS as $dirPath) {
            $items = DirManager::inst($dirPath)->getDirContent(null, $filterName);
            $RESULT = array_merge($RESULT, array_values($items));
        }
        $this->LOGGER->info('Loading finished, items count: {}. Taken time: {}sec.', count($RESULT), $this->PROFILER->stop()->getTime());
        $this->LOGGER->info('');
        return $RESULT;
    }

}

?>
