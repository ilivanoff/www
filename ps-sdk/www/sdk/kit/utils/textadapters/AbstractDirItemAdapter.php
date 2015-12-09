<?php

/**
 * Базовый класс для всех адаптеров над элементом лиректории
 */
abstract class AbstractDirItemAdapter {

    /**
     * Элемент директории, над которым работает данный адаптер
     *
     * @var DirItem
     */
    protected $di;

    public final function __construct(DirItem $di) {
        $this->di = $di;
        $this->onInit($di);
    }

    /**
     * Метод вызывается для подготовки адаптера
     */
    protected abstract function onInit(DirItem $di);

    /**
     * 
     * @return DirItem
     */
    public final function getDi() {
        return $this->di;
    }

}

?>
