<?php

/**
 * Базовый класс, возвращаемый при удачной обработке Ajax-запроса.
 */
class AjaxSuccess implements FormSuccess {

    private $result;

    public function __construct($result = 'OK') {
        $this->result = $result;
    }

    public function getJsParams() {
        return $this->result;
    }

}

?>