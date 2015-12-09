<?php

class LibBubbleItem {

    private $libItem;
    private $tlItem;

    public function __construct(LibItemDb $libItem, TimeLineItem $tlItem = null) {
        $this->libItem = $libItem;
        $this->tlItem = $tlItem;
    }

    public function getCover() {
        //TODO - наличие Cover не зависит от наличия TimeLineItem
        return $this->tlItem ? $this->tlItem->getImage() : null;
    }

    public function getDates() {
        return $this->tlItem ? $this->tlItem->getDates() : null;
    }

    public function getTitle() {
        return $this->libItem->getName();
    }

    public function getContent() {
        return $this->libItem->getContent();
    }

}

?>
