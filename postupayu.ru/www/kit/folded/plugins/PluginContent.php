<?php

class PluginContent {

    private $content; //Содержимое
    private $postData; //Данные, которые будут переданы в PostFetchingContext

    public function __construct($content, $postData = null) {
        $this->content = $content;
        $this->postData = $postData;
    }

    public function getContent() {
        return $this->content;
    }

    public function getPostData() {
        return $this->postData;
    }

}

?>