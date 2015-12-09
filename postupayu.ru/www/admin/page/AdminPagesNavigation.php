<?php

class AdminPagesNavigation {

    private $html = '';

    public function addItem($content, $level) {
        $this->html .= PsHtml::html2('li', array('class' => "level$level"), $content);
    }

    public function getHtml(array $attrs = array()) {
        $attrs['id'] = 'adminPages';
        $attrs['class'][] = 'sections';
        return PsHtml::html2('ul', $attrs, $this->html);
    }

}

?>