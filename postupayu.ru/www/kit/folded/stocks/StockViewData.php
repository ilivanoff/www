<?php

class StockViewData {

    private $html;
    private $jsParams;

    function __construct($html, $jsParams = array()) {
        $this->html = $html;
        $this->jsParams = $jsParams;
    }

    public function getHtml() {
        return $this->html;
    }

    public function getJsParams() {
        return $this->jsParams;
    }

}

?>
