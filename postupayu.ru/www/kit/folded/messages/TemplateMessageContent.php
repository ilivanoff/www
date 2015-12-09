<?php

/**
 * Содержимое шаблонного сообщения.
 * В нём передаются тема и, собственно, контент.
 *
 * @author azazello
 */
class TemplateMessageContent {

    private $theme;
    private $content;

    function __construct($theme, $content) {
        $this->theme = $theme;
        $this->content = $content;
    }

    public function getTheme() {
        return $this->theme;
    }

    public function getContent() {
        return $this->content;
    }

}

?>