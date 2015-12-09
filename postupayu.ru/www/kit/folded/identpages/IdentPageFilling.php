<?php

/**
 * Содержимое, которое должна вернуть страница после своей обработки.
 */
class IdentPageFilling {

    /**
     * Параметры сматри ,которые будут переданы в шаблон для фетчинга.
     */
    private $smartyParams;

    /** Параметры javascript */
    private $jsParams;

    public function __construct(array $smartyParams = array(), array $jsParams = array()) {
        $this->smartyParams = $smartyParams;
        $this->jsParams = $jsParams;
    }

    public function getSmartyParams() {
        return $this->smartyParams;
    }

    public function getJsParams() {
        return $this->jsParams;
    }

}

?>