<?php

class TimeLineResources extends FoldedResources {

    protected $RESOURCE_TYPES_ALLOWED = array(self::RTYPE_PHP, self::RTYPE_JS, self::RTYPE_CSS, self::RTYPE_TPL);

    public function getEntityName() {
        return 'Хронологическая шкала';
    }

    public function getFoldingGroup() {
        return 'timelines';
    }

    public function getFoldingType() {
        return 'tl';
    }

    public function getFoldingSubType() {
        
    }

    protected function isIncludeToList($ident, $list) {
        
    }

    protected function onEntityChangedImpl($ident) {
        
    }

    public function getFoldedEntityPreview($ident) {
        return array(
            'info' => '',
            'content' => $this->fetchTplWithResources($ident)
        );
    }

}

?>