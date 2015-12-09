<?php

/**
 *
 * @author azazello
 */
class TL_poets extends TimeLineFoldingBuilder {

    public function getTitle() {
        return 'Великие поэты';
    }

    public function getTimeLineFolding() {
        return PoetsManager::inst();
    }

}

?>