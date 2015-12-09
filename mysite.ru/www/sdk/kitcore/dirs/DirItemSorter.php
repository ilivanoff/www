<?php

/*
 * Сортировка DirItem`ов.
 * Пример использования:
 * DirItemSorter::inst()->sort($items, DirItemSorter::BY_SIZE);
 */

class DirItemSorter {

    const BY_SIZE = 'bySize';
    const BY_WIDTH = 'byWidth';
    const BY_HEIGHT = 'byHeight';
    const BY_NAME = 'byName';
    const BY_NAME_CS = 'byNameCs'; //Чувствителен к регистру

    private function bySize(DirItem $i1, DirItem $i2) {
        return $i1->getSize() > $i2->getSize() ? 1 : -1;
    }

    private function byWidth(DirItem $i1, DirItem $i2) {
        return $i1->getImageAdapter()->getWidth() > $i2->getImageAdapter()->getWidth() ? 1 : -1;
    }

    private function byHeight(DirItem $i1, DirItem $i2) {
        return $i1->getImageAdapter()->getHeight() > $i2->getImageAdapter()->getHeight() ? 1 : -1;
    }

    private function byName(DirItem $i1, DirItem $i2) {
        return strcasecmp($i1->getName(), $i2->getName());
    }

    private function byNameCs(DirItem $i1, DirItem $i2) {
        return strcmp($i1->getName(), $i2->getName());
    }

    public function sort(&$items, $type) {
        usort($items, array($this, $type));
    }

    /*
     * СИНГЛТОН
     */

    private static $instance = NULL;

    /** @return DirItemSorter */
    public static function inst() {
        if (self::$instance == NULL) {
            self::$instance = new DirItemSorter();
        }
        return self::$instance;
    }

    private function __construct() {
        
    }

}

?>
