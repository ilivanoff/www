<?php

/**
 * Кдасс для построения навигации по сдминским страницам
 */
class AdminPageNavigation extends AbstractSingleton {

    private $path = array();
    private $current = null;

    public function addPath($href, $text) {
        $this->path[] = PsHtml::a(array('href' => $href), $text);
    }

    public function setCurrent($text) {
        $this->current = $text;
    }

    public function html() {
        $path = $this->path;
        if ($this->current) {
            $path[] = PsHtml::span(array(), $this->current);
        }
        return empty($path) ? '' : PsHtml::div(array('class' => 'ps-page-nav'), implode('', $path));
    }

    /** @return AdminPageNavigation */
    public static function inst() {
        return parent::inst();
    }

}

?>