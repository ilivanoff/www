<?php

/**
 * Класс подключает все базовые классы проекта - логгер, менеджер директорий и т.д.
 *
 * @author azazello
 */
final class PsCoreIncluder {

    /**
     * Признак выполнения подключения
     */
    private $CALLED = false;

    /**
     * Карта `Название класса`=>`Путь к классу` из kitcore. Пример:
     * [PsUtil] => C:/www/postupayu.ru/www/kitcore/utils/PsUtil.php
     */
    private $PATHES;

    /**
     * Карта `Название класса`=>`Способ подключения`. Пример:
     * [PsUtil] => Directly
     * [Destructable] => PsCoreIncluder::_autoload
     */
    private $INCLUDED;

    /**
     * Основной метод, выполняющий подключение всех классов из kitcore.
     * Нужно постараться сделать так, чтобы он отрабатывал максимально быстро,
     * так как мы используем его вместо принудительного подключения всех классов
     * через require_once.
     * Возможно для ускорения работы однажды мы так и сделаем.
     */
    public function includeCore() {
        if ($this->CALLED) {
            die(__CLASS__ . ' cannot be called twice.');
        }
        $this->CALLED = true;

        /*
         * Засекаем время подключения
         */
        $time = microtime(true);

        /*
         * Сразу подключаем  настройки и утилитные функции
         */
        include_once 'Globals.php';
        include_once 'Defines.php';

        /*
         * Инициализируем глобальные коллекции, так как доступ к ним будет
         * осуществляться из всех методов данного класса.
         */
        $this->PATHES = array();
        $this->INCLUDED = array();

        /*
         * Собираем классы из kitcore, которые нужно подключить.
         * Пути к php-классам будут собраны в переменную класса, чтобы к ним был
         * доступ и из функции autoload.
         */
        self::loadClassPath(__DIR__, $this->PATHES, true);

        /*
         * Подключим слушатель ошибки загрузки файлов, так как классы могут быть
         * загружены не в правильном порядке.
         */
        spl_autoload_register(array($this, '_autoload'));

        /*
         * Начинаем подключение
         */
        foreach ($this->PATHES as $name => $path) {
            if (!array_key_exists($name, $this->INCLUDED)) {
                /*
                 * Поставим отметку о том, что файл загружен напрямую.
                 * Если этого не случится, то в функции автоподключение это 
                 * будет переопределено.
                 */
                $this->INCLUDED[$name] = 'Directly';
                include_once $path;
            }
        }

        /*
         * Отключим обработчки ошибок, так как он нам больше не нужен.
         */
        spl_autoload_unregister(array($this, '_autoload'));

        /*
         * Теперь можно использовать логгер для вывода информации о логировании
         */
        if (PsLogger::isEnabled()) {
            $time = microtime(true) - $time;
            PsLogger::inst(__FILE__)->info('File pathes: ' . print_r($this->PATHES, true));
            PsLogger::inst(__FILE__)->info('Include order: ' . print_r($this->INCLUDED, true));
            PsLogger::inst(__FILE__)->info('Total time: ' . $time);
        }

        /*
         * Освободим память
         */
        unset($this->PATHES);
        unset($this->INCLUDED);
    }

    /**
     * Метод рекурсивно собирает все классы в директории.
     * 
     * @param string $dirAbsPath - путь к директории
     * @param array $classes - карта [PsUtil] => [C:/www/postupayu.ru/www/kitcore/utils/PsUtil.php]
     * @param bool $skipDirClasses - пропускать ли классы в корневой директории.
     * Флаг позволит не подключать классы, лежащие в корне kitcore,
     * так как их мы подключим сами (Globals, Defines, PsCoreIncluder)
     */
    public static function loadClassPath($dirAbsPath, array &$classes, $skipDirClasses) {
        if (!is_dir($dirAbsPath)) {
            return; //---
        }

        $dir = openDir($dirAbsPath);

        while ($file = readdir($dir)) {
            if (!is_valid_file_name($file)) {
                continue;
            }

            $isphp = ends_with($file, '.php');
            if ($isphp && $skipDirClasses) {
                continue;
            }

            $path = next_level_dir($dirAbsPath, $file);
            if ($isphp) {
                $classes[cut_string_end($file, '.php')] = $path;
            } else {
                self::loadClassPath($path, $classes, false);
            }
        }

        closedir($dir);
    }

    /**
     * Функция автолоадера, регистрируемого для разруливания зависимостей.
     */
    private function _autoload($class) {
        $this->INCLUDED[$class] = __CLASS__ . '::' . __FUNCTION__;
        include_once $this->PATHES[$class];
    }

    /*
     * СИНГЛТОН
     */

    private static $inst;

    /** @return PsCoreIncluder */
    public static function inst() {
        return self::$inst ? self::$inst : self::$inst = new PsCoreIncluder();
    }

}

?>