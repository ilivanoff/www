<?php

/**
 * Файл с тем, что необходимо выполнить - ToDo файл.
 * Основназ задача класса - инкапсулирувать всю работу с данным файлом и предоставлять информацию о нём.
 */
class ToDoFile {

    /** @var DirItem */
    private $di;

    /** @var ToDoFile */
    private static $todo;

    /** @return ToDoFile */
    public static function inst() {
        if (!isset(self::$todo)) {
            PsDefines::assertProductionOff(__CLASS__);
            self::$todo = new ToDoFile();
        }
        return self::$todo;
    }

    private function __construct() {
        $this->di = DirItem::inst(__DIR__, __CLASS__, 'tpl')->touchIfNotFile();
    }

    public function getMtime() {
        return $this->di->getModificationTime();
    }

    public function getContents() {
        return $this->di->getFileContents();
    }

    public function getHtml() {
        return ContentHelper::getContent(PSSmarty::template($this->di));
    }

    public function isCanSave($mtime) {
        return is_numeric($mtime) && (1 * $mtime >= $this->getMtime());
    }

    public function save($content, $mtime) {
        check_condition($this->isCanSave($mtime), $this->di->getNameNoExt() . ' был изменён с момента открытия');
        $this->di->writeToFile($content, true);
    }

}

?>
