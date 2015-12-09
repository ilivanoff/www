<?php

/*
 * В этот класс для удобства вынесено всё, что "знает" информацию о плагинах
 */

class PluginResources extends FoldedResources implements ImagedFolding, PointsGiverFolding {

    function defaultDim() {
        return '36x36';
    }

    public function getEntityName() {
        return 'Плагин';
    }

    public function getFoldingType() {
        return 'pl';
    }

    public function getFoldingSubType() {
        return null;
    }

    public function getFoldingGroup() {
        return 'plugins';
    }

    protected function isIncludeToList($ident, $list) {
        return true;
    }

    protected function onEntityChangedImpl($ident) {
        
    }

}

?>