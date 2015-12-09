<?php

/**
 * Содержимое, которое должем вернуть ClientBox после построения.
 *
 * @author azazello
 */
class ClientBoxFilling {

    private $title;
    private $cover;
    private $href;
    private $smartyParams;
    private $jsParams;

    public function __construct($title, $showCover = true, $hrefOrPageCode = null, array $smartyParams = array(), $jsParams = null) {
        $this->title = $title;
        $this->cover = !!$showCover;
        $this->href = is_numeric($hrefOrPageCode) ? WebPage::inst($hrefOrPageCode)->getUrl() : $hrefOrPageCode;
        $this->smartyParams = $smartyParams;
        $this->jsParams = $jsParams;
    }

    public function isCover() {
        return $this->cover;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getHref() {
        return $this->href;
    }

    public function getSmartyParams() {
        return $this->smartyParams;
    }

    public function getJsParams() {
        return $this->jsParams;
    }

}

?>
