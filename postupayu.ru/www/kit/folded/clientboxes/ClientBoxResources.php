<?php

abstract class ClientBoxResources extends FoldedResources implements ImagedFolding {

    protected $RESOURCE_TYPES_ALLOWED = array(self::RTYPE_JS, self::RTYPE_CSS, self::RTYPE_PHP, self::RTYPE_TPL);

    function defaultDim() {
        return '20x20';
    }

    protected function isIncludeToList($ident, $list) {
        return true;
    }

    protected function onEntityChangedImpl($ident) {
        
    }

    public function getEntityName() {
        return 'Виджет справа';
    }

    public function getFoldingGroup() {
        return 'clientboxes';
    }

    public function getFoldingSubType() {
        return null;
    }

    public function getFoldingType() {
        return 'cb';
    }

}

?>