<?php

/**
 * Ресурсы фолдинга 'Диалоговые окна' (dg)
 *
 * @author auto generated
 */
abstract class DialogResources extends FoldedResources {

    //Используемые типы ресурсов
    protected $RESOURCE_TYPES_ALLOWED = array(self::RTYPE_JS, self::RTYPE_CSS, self::RTYPE_PHP, self::RTYPE_TPL);

    protected function isIncludeToList($ident, $list) {
        
    }

    protected function onEntityChangedImpl($ident) {
        
    }

    public function getEntityName() {
        return 'Диалоговые окна';
    }

    public function getFoldingGroup() {
        return 'dialog';
    }

    public function getFoldingType() {
        return 'dg';
    }

    public function getFoldingSubType() {
        return null;
    }

}

?>