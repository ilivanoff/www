<?php

/**
 * Панель с кнопками управления сверху в панели
 *
 * @author azazello
 */
class IdentPagesClientPanel implements PluggablePanel {

    private $pages;

    public function __construct($pages) {
        $this->pages = $pages;
    }

    public function getHtml() {
        $hrefs = array();
        /** @var BaseIdentPage */
        foreach ($this->pages as $page) {
            $hrefs[] = $page->getIdentPageHref();
        }
        return implode('', $hrefs);
    }

    public function getJsParams() {
        
    }

    public function getSmartyParams4Resources() {
        
    }

}

?>
