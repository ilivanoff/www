<?php

/**
 * Класс для работы с глобальными настройками, задаваемыми в файле
 * Globals.php.
 * 
 * Для максимально быстрой работы мы храним глобальные настройки именно в виде
 * констант php.
 */
final class PsGlobals extends AbstractSingleton {

    /** @var PsLoggerInterface */
    private $LOGGER;

    /** @var DirItem */
    private $DI;

    /** @var array */
    private $GLOBALS;

    /** Время последней модификации файла глобальных настроек */
    private $FileMtime;

    /**
     * Обновление $FileMtime. Производится:
     * 1. До загрузки настроек из файла.
     * 2. После сохранения настроек в файл
     */
    private function FileMtimeUpdate() {
        $this->FileMtime = $this->DI->getModificationTime();
        check_condition(is_numeric($this->FileMtime) && ($this->FileMtime > 0), 'Файл глобальных настроек не существует');
    }

    /**
     * Загружает глобальные настройки из файла и кеширует их в массиве GLOBALS.
     * Данный метод вызывается ТОЛЬКО при создании экземпляра класса.
     */
    private function load() {
        check_condition(!is_array($this->GLOBALS), 'Недопустима повторная загрузка глобальных настроек');
        $this->GLOBALS = array();
        $this->FileMtimeUpdate();

        $comment = array();
        foreach ($this->DI->getFileLines() as $line) {
            $line = trim($line);
            if (!$line || starts_with($line, '/*') || ends_with($line, '*/')) {
                continue;
            }
            if (starts_with($line, '*')) {
                $line = trim(first_char_remove($line));
                if ($line) {
                    $comment[] = $line;
                }
                continue;
            }
            if (starts_with($line, 'define')) {
                $name = trim(array_get_value(1, explode("'", $line, 3)));
                check_condition($name && defined($name), "Ошибка разбора файла глобальных настроек: свойство [$name] не определено.");
                $this->GLOBALS[$name] = new PsGlobalProp($name, implode(' ', $comment));
                $comment = array();
                continue;
            }
        }
    }

    /**
     * Возвращает список глобальных свойств
     */
    public function getProps() {
        return $this->GLOBALS;
    }

    /**
     * Возвращает глобальные настройки в виде массива ключ-значение
     */
    public function getPropsKeyValue() {
        $result = array();
        /* @var $prop PsGlobalProp */
        foreach ($this->GLOBALS as $name => $prop) {
            $result[$name] = $prop->getValue();
        }
        return $result;
    }

    /**
     * @return PsGlobalProp
     */
    public function getProp($name) {
        check_condition(array_key_exists($name, $this->GLOBALS), "Глобальная настройка [$name] не зарегистрирована");
        return $this->GLOBALS[$name];
    }

    /**
     * Проверяет, есть ли модифицированные свойства
     */
    private function hasModified() {
        /* @var $prop PsGlobalProp */
        foreach ($this->GLOBALS as $prop) {
            if ($prop->isDearty()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Возсращает содержимое Globals.php, готовое к сохранению в файл
     */
    public function getPhpFileContents() {
        $content = "<?php\n\n";
        /* @var $prop PsGlobalProp */
        foreach ($this->GLOBALS as $prop) {
            $content .= $prop->getFileBlock();
            $content .= "\n";
        }
        $content .= '?>';
        return $content;
    }

    /**
     * Сохраняет глобальные настройки в Globals.php
     */
    public function save2file() {
        if (!$this->hasModified()) {
            return; //--- Нет модифицированных свойств
        }
        $content = $this->getPhpFileContents();
        check_condition($this->FileMtime === $this->DI->getModificationTime(), 'Файл глобальных настроек был изменён с момента загрузки');
        $this->DI->putToFile($content);
        $this->FileMtimeUpdate();

        //"Коммитим" настройки
        /* @var $prop PsGlobalProp */
        foreach ($this->GLOBALS as $prop) {
            $prop->commit();
        }
    }

    /**
     * Обновляет глобальные настройки из файла.
     * Автоматически производится сохранение настроек в файл.
     */
    public function updateProps(array $globals) {
        foreach ($globals as $name => $value) {
            $this->getProp($name)->setValue($value);
        }
        $this->save2file();
    }

    /** @return PsGlobals */
    public static function inst() {
        return parent::inst();
    }

    protected function __construct() {
        $this->LOGGER = PsLogger::inst(__CLASS__);
        $this->DI = DirItem::inst('kitcore', 'Globals.php');
        $this->load();
    }

}

?>