<?php

/**
 * Содержимое, которое будет передано на клиена через ajax как результат обработки страницы.
 */
class IdentPageContent {

    /** Содержимое, которое будет добавлено в управляющий див */
    private $div;

    /** Параметры javascript */
    private $jsParams;

    public function __construct($div, array $jsParams = array()) {
        $this->div = $div;
        $this->jsParams = $jsParams;
    }

    public function getContent() {
        return $this->div;
    }

    public function getJsParams() {
        return $this->jsParams;
    }

    public function toArray4Json() {
        return array(
            'ctt' => $this->div, //Page content
            'jsp' => $this->jsParams// JavaScript params
        );
    }

}

?>
