<?php

/**
 *
 * @author azazello
 */
class TL_scientists extends TimeLineFoldingBuilder {

    public function getTitle() {
        return 'Великие учёные';
    }

    public function getTimeLineFolding() {
        return ScientistsManager::inst();
    }

}

?>