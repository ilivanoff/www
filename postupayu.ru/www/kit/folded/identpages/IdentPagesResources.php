<?php

abstract class IdentPagesResources extends FoldedResources implements ImagedFolding {

    protected $RESOURCE_TYPES_ALLOWED = array(self::RTYPE_JS, self::RTYPE_CSS, self::RTYPE_PHP, self::RTYPE_TPL);

    function defaultDim() {
        return '16x16';
    }

    public function getEntityName() {
        return 'Загружаемое окно';
    }

    public function getFoldingGroup() {
        return 'idents';
    }

    public function getFoldingType() {
        return 'ip';
    }

    public function getFoldingSubType() {
        return null;
    }

    protected function onEntityChangedImpl($ident) {
        
    }

}

?>