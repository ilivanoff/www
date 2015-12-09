<?php

class PSFormResources extends FoldedResources {

    protected $RESOURCE_TYPES_ALLOWED = array(self::RTYPE_JS, self::RTYPE_CSS, self::RTYPE_PHP, self::RTYPE_TPL);

    /** @return AbstractForm */
    public function getForm($formId) {
        return $this->getEntityClassInst($formId);
    }

    protected function isIncludeToList($ident, $list) {
        return false;
    }

    public function getEntityName() {
        return 'Форма';
    }

    public function getFoldingGroup() {
        return 'forms';
    }

    public function getFoldingType() {
        return 'form';
    }

    public function getFoldingSubType() {
        
    }

    protected function onEntityChangedImpl($ident) {
        
    }

    public function getFoldedEntityPreview($ident) {
        $content = $this->getForm($ident)->fetch();
        return array(
            'info' => $ident,
            'content' => $content
        );
    }

}

?>