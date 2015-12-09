<?php

/**
 * Директория с классами .php
 * 
 * В этот класс вынесено всё для удобства работы - поиск класса, построение и перестроение класспаса.
 *
 * @author azazello
 */
final class AutoloadDir {

    /**
     * @var PsLoggerInterface Логгер используем общий с Autoload
     */
    private $LOGGER;

    /**
     * @var DirItem Диретория с классами
     */
    private $classesDir;

    /**
     * @var DirItem Файл с путями к классам
     */
    private $classPathFile;

    /**
     * @var array Карта - Название класса => Абсолютный путь к классу
     */
    private $pathes = null;

    /**
     * @var bool Признак, был ли class path перестроен
     */
    private $rebuilded = false;

    public function __construct(DirItem $dir) {
        $this->LOGGER = Autoload::getLogger();
        $this->classesDir = $dir;
        $this->classPathFile = DirManager::autogen('classpath')->getDirItem(null, unique_from_path($dir->getRelPath()));
    }

    /**
     * Признак, перестраивался ли класспас для этой директории
     * 
     * @return bool
     */
    public function isRebuilded() {
        return $this->rebuilded;
    }

    /**
     * Метод подготавливает директорию к работе. Его задача - заполнить пути $pathes.
     * 
     * @return AutoloadDir
     */
    private function init() {
        if (!is_array($this->pathes)) {
            $this->pathes = $this->classPathFile->getArrayFromFile();
            if (is_array($this->pathes)) {
                //Пути к классам загружены из кеша
            } else {
                //Строим карту классов директории
                $this->rebuild();
            }
        }
        return $this;
    }

    /**
     * Перестраиваем и сохраняем classpath в файл.
     * Вынесли это действие в отдельный метод, чтобы можно было вывести classpath в файл (для дебага).
     * 
     * @return AutoloadDir
     */
    public function rebuild() {
        if (!$this->rebuilded) {
            $this->rebuilded = true;

            $s = Secundomer::startedInst();

            $this->pathes = array();
            PsCoreIncluder::loadClassPath($this->classesDir->getAbsPath(), $this->pathes, false);
            $this->classPathFile->saveArrayToFile($this->pathes);
            $this->classPathFile->getSibling($this->classPathFile->getName() . '_arr')->putToFile(print_r($this->pathes, true));

            $this->LOGGER->info("$this rebuilded in {$s->stopAndGetAverage()} seconds.");
        }
        return $this;
    }

    /**
     * Поиск пути к классу по его названию.
     * 
     * @param string $class - название класса
     * @return type
     */
    public function getClassPath($class) {
        $path = array_get_value($class, $this->init()->pathes);
        if ($path) {
            /*
             * Нашли класс! 
             * Если файл был вычитан из кеша, проверим, существует ли файл до сих пор.
             * Возможно, его переместили?
             */
            if (!$this->rebuilded && !file_exists($path)) {
                $this->LOGGER->info("Class [$class] was cached for $this, but now cannot be found on [$path]. Rebuilding...");

                $path = array_get_value($class, $this->rebuild()->pathes);
                if (!$path) {
                    $this->LOGGER->info("Class [$class] not found for $this now.");
                    return null;
                }
            }
        }

        $this->LOGGER->info("Class [$class] {} for $this.", $path ? 'found' : 'not found');

        return $path;
    }

    public function __toString() {
        return __CLASS__ . ' [' . $this->classesDir->getAbsPath() . ']';
    }

}

?>