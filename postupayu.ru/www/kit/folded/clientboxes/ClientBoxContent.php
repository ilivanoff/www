<?php

/**
 * Содержимое, которое будет передано на клиена через ajax как результат обработки страницы.
 */
class ClientBoxContent {

    /** Содержимое, которое будет добавлено в управляющий див */
    private $div;

    /** Параметры javascript */
    private $jsParams;

    public function __construct($div, $jsParams) {
        $this->div = $div;
        $this->jsParams = $jsParams;
    }

    public function getDiv() {
        return $this->div;
    }

    public function getJsParams() {
        return $this->jsParams;
    }

}

?>