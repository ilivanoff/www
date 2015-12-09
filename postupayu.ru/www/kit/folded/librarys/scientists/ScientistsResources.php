<?php

/*
 * Ресурсы об учёных
 */

abstract class ScientistsResources extends LibResources {

    public function getEntityName() {
        return 'Учёные';
    }

    function defaultDim() {
        return '210x';
    }

    protected function isIncludeToList($ident, $list) {
        
    }

    protected function onEntityChangedImpl($ident) {
        
    }

    public function getFoldingGroup() {
        return 'librarys/scientists';
    }

    public function getFoldingSubType() {
        return 's';
    }

}

?>