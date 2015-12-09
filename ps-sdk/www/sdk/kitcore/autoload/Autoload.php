<?php

/**
 * Очевидно, что наш класслоадер будет вызываться в разы чаще, чем класслоадеры подключаемых библиотек,
 * так что регистрируем его первым.
 */
final class Autoload {
    /** Директории подключаемых классов */

    const DIR_KIT = 'sdk/kit';
    const DIR_ADMIN = 'admin';
    const DIR_TESTS = 'tests';
    const DIR_CLASSES = 'classes';

    /** @var PsLoggerInterface */
    private $LOGGER;

    /** callable объект класслоадера */
    private $AUTOLOAD;

    /** @var DirItem */
    private $COMMON_EXCLUDED_DI;

    /** Признак - зарегистрирован ли автолоадер */
    private $IS_REGISTERED = false;

    /**
     * Регистрирует функцию {@link Autoload::load} как класслоадер
     */
    public function register() {
        if ($this->IS_REGISTERED) {
            return; //---
        }

        check_condition(spl_autoload_register($this->AUTOLOAD), 'Could not register class autoload function');
        $this->IS_REGISTERED = true;
        $this->LOGGER->info('Class loader is REGISTERED, spl functions count: ' . count(spl_autoload_functions()));
    }

    /**
     * Снимает регистрирацию функции {@link Autoload::load} как класслоадера
     */
    public function unregister() {
        if (!$this->IS_REGISTERED) {
            return; //---
        }

        check_condition(spl_autoload_unregister($this->AUTOLOAD), 'Could not unregister class autoload function');
        $this->IS_REGISTERED = false;
        $this->LOGGER->info('Class loader is UNREGISTERED, spl functions count: ' . count(spl_autoload_functions()));
    }

    protected function load($className) {
        $path = $this->getClassPath($className);
        if ($path) {
            /*
             * В кеше нет класса, тем не менее самостоятельно не выбрасываем ошибку, т.к.
             * могут быть другие загрузчики.
             * 
             * check_condition($this->casch->hasKey($fullClassName), "В кеше загрузчика нет класса [$fullClassName]");
             * 
             * TODO научиться бы ловить эту ошибку...
             */
            require_once($path);
        }
    }

    /**
     * Метод возвращает логгер для того, чтобы в него могли писать другие подсистемы,
     * занимающиеся поиском классов.
     * 
     * @return PsLoggerInterface
     */
    public static function getLogger() {
        return PsLogger::inst(__CLASS__);
    }

    /**
     * Директории, в которых будет производиться поиск классов
     */
    private $DIRS = array();

    /**
     * Метод регистрирует директорию, как доступную для поиска и подключения классов
     * 
     * @param type $dirName - название директории (одна из констант DIR_ данного класса)
     */
    public function registerBaseDir($path, $required = true) {
        //Получим DirItem, соответствующий нашей директории
        $di = DirItem::inst(next_level_dir($path, DIR_SEPARATOR));
        //Проверим - может уже подключили?
        if (array_key_exists($di->getRelPath(), $this->DIRS)) {
            return; //---
        }
        //$dirAbsPath = [C:/www/postupayu.ru/www/kit/]
        $dirAbsPath = $di->getAbsPath();
        //Проверим, является ли путь - директорией
        if (!$di->isDir()) {
            check_condition(!$required, "Invalid class path dir given: [$dirAbsPath]");
            return; //---
        }
        //Отлогируем
        $this->LOGGER->infoBox("CLASS PATH DIR [$dirAbsPath] REGISTERED");
        //Сохраним ссылку на директорию
        $this->DIRS[$di->getRelPath()] = new AutoloadDir($di);
    }

    public function registerAdminBaseDir() {
        $this->registerBaseDir(self::DIR_ADMIN, false);
    }

    public function registerTestsBaseDir() {
        $this->registerBaseDir(self::DIR_TESTS);
    }

    /**
     * Основной метод, возвращающий путь к классу
     */
    public function getClassPath($className) {
        $className = cut_string_end($className, '.php');

        $this->LOGGER->info('');
        $this->LOGGER->info("Searching path for class [$className].");
        $path = $this->getClassPathImpl($className);
        $this->LOGGER->info("Path [$path] returned.");
        return $path;
    }

    /**
     * Основные исключённые классы для всех директорий
     */
    private $COMMON_EXCLUDED;

    private function loadCommonExcluded($forceReload = false) {
        if ($forceReload || !is_array($this->COMMON_EXCLUDED)) {
            $this->COMMON_EXCLUDED = to_array($this->COMMON_EXCLUDED_DI->getArrayFromFile());
        }
        return $this->COMMON_EXCLUDED;
    }

    private function isCommonExcluded($class) {
        return in_array($class, $this->loadCommonExcluded());
    }

    private function cleanCommonExcluded() {
        $this->COMMON_EXCLUDED = null;
        $this->COMMON_EXCLUDED_DI->remove();
    }

    private function saveCommonExcluded($class) {
        $pathes = $this->loadCommonExcluded(true);
        if (in_array($class, $pathes)) {
            //Класс уже отмечен, как исключённый
            return; //---
        }
        $pathes[] = $class;
        $this->COMMON_EXCLUDED = $pathes;
        $this->COMMON_EXCLUDED_DI->saveArrayToFile($pathes);
        $this->COMMON_EXCLUDED_DI->getSibling($this->COMMON_EXCLUDED_DI->getName() . '_arr')->putToFile(print_r($pathes, true));
    }

    /**
     * Основной метод, получающий абсолютный путь к классу.
     * 
     * @param str $class
     * @return str - Путь к файлу
     */
    private function getClassPathImpl($class) {
        /* @var $classPathDir AutoloadDir */
        foreach ($this->DIRS as $classPathDir) {
            //ИЩЕМ КЛАСС
            $path = $classPathDir->getClassPath($class);
            if ($path) {
                /*
                 * Мы нашли класс!
                 * Если он отмечен, как исключённый для всех - сборосим список исключённых.
                 * Могло так случиться, что класс загружался без подключённой директорией (например admin).
                 */
                if ($this->isCommonExcluded($class)) {
                    $this->LOGGER->info("Class [$class] was marked as excluded, but now found in $classPathDir. Clearing excluded.");
                    $this->cleanCommonExcluded();
                }

                return $path;
            }
        }

        //ПОПРОБУЕМ ВОСПОЛЬЗОВАТЬСЯ ФОЛДИНГАМИ
        $path = Handlers::tryGetFoldedEntityClassPath($class);
        if ($path) {
            $this->LOGGER->info("Class path for [$class] found with folded resources.");
            return $path;
        }

        //МЫ НЕ НАШЛИ НАШ КЛАСС В ПУТЯХ, А МОЖЕТ ОН ИСКЛЮЧЁН ДЛЯ ВСЕХ?
        if ($this->isCommonExcluded($class)) {
            $this->LOGGER->info("Class [$class] is common excluded.");
            return null;
        }

        //КЛАСС НЕ ЯВЛЯЕТСЯ КЛАССОМ СУЩНОСТИ ФОЛДИНГА. ПЕРЕСТРОИМ КЛАССПАСЫ ДЛЯ ЗАКЕШОРОВАННЫХ ДИРЕКТОРИЙ ИЛИ ОТМЕТИМ КЛАСС, КАК ИСКЛЮЧЁННЫЙ
        /* @var $classPathDir AutoloadDir */
        foreach ($this->DIRS as $classPathDir) {
            if ($classPathDir->isRebuilded()) {
                //Данную директорию уже перезагружали, в ней класса точно нет
                continue;
            }

            //Если файл не найден, но при этом класспас не перестраивался, то необходимо поискать его заново
            $this->LOGGER->info("Class [$class] not excluded, need to check $classPathDir again.");

            $path = $classPathDir->rebuild()->getClassPath($class);
            if ($path) {
                //Ура, наши класс в одной из директорий
                $this->LOGGER->info("Class [$class] found for $classPathDir after rebuild.");
                return $path;
            }
        }
        $this->saveCommonExcluded($class);

        $this->LOGGER->info("Class [$class] is marked as excluded, null is returned.");
        return null;
    }

    /*
     * 
     * СИНГЛТОН
     * 
     */

    private static $inst;

    /** @return Autoload */
    public static function inst() {
        return self::$inst ? self::$inst : self::$inst = new Autoload();
    }

    /**
     * КОНСТРУКТОР
     */
    private function __construct() {
        $this->LOGGER = self::getLogger();
        $this->AUTOLOAD = array($this, 'load');
        $this->COMMON_EXCLUDED_DI = DirManager::autogen('classpath')->getDirItem(null, 'excluded');
        $this->registerBaseDir(self::DIR_KIT, false); //TODO
        $this->registerBaseDir(self::DIR_CLASSES, false);
        //TODO
        $this->registerBaseDir('kit', false);
    }

}

?>