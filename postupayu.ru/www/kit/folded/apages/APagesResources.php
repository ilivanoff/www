<?php

class APagesResources extends FoldedResources {

    protected $RESOURCE_TYPES_ALLOWED = array(self::RTYPE_JS, self::RTYPE_CSS, self::RTYPE_PHP, self::RTYPE_TPL);

    protected function isIncludeToList($ident, $list) {
        
    }

    protected function onEntityChangedImpl($ident) {
        
    }

    public function getEntityName() {
        return 'Админские страницы';
    }

    public function getFoldingGroup() {
        return 'apages';
    }

    public function getFoldingType() {
        return 'ap';
    }

    public function getFoldingSubType() {
        
    }

    /** @return APagesResources */
    public static function inst() {
        return parent::inst();
    }

}

?>