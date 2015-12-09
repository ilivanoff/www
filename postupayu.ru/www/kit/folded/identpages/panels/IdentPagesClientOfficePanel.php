<?php

/**
 * Панель в личном кабинете пользователя (справа)
 *
 * @author azazello
 */
class IdentPagesClientOfficePanel implements PluggablePanel {

    private $pages;

    public function __construct($pages) {
        $this->pages = $pages;
    }

    public function getHtml() {
        $hrefs = array();
        /** @var BaseIdentPage */
        foreach ($this->pages as $page) {
            $href = $page->smallOfficeLiContent();
            if ($href) {
                $hrefs[] = "<li>$href</li>";
            }
        }
        return '<ul>' . implode('', $hrefs) . '</ul>';
    }

    public function getJsParams() {
        
    }

    public function getSmartyParams4Resources() {
        
    }

}

?>
