<?php

class ShowcasesCtrlFoldedResources extends FoldedResources {

    protected $RESOURCE_TYPES_ALLOWED = array(self::RTYPE_PHP, self::RTYPE_JS, self::RTYPE_CSS, self::RTYPE_TPL);

    protected function isIncludeToList($ident, $list) {
        return true;
    }

    protected function onEntityChangedImpl($ident) {
        
    }

    public function getEntityName() {
        return 'Управление предпросмотром постов';
    }

    public function getFoldingGroup() {
        return 'showcasesctrls';
    }

    public function getFoldingType() {
        return 'sc';
    }

    public function getFoldingSubType() {
        return null;
    }

}

?>