<?php

/**
 * Ресурсы фолдинга '{$EntityName}' ({$funique})
 *
 * @author auto generated
 */
abstract class {$ResourcesClass} extends FoldedResources {$implements}{

    //Используемые типы ресурсов
    protected $RESOURCE_TYPES_ALLOWED = array({$rtypes});

    protected function isIncludeToList($ident, $list) {
        
    }

    protected function onEntityChangedImpl($ident) {
        
    }

    public function getEntityName() {
        return '{$EntityName}';
    }

    public function getFoldingGroup() {
        return '{$FoldingGroup}';
    }

    public function getFoldingType() {
        return '{$FoldingType}';
    }

    public function getFoldingSubType() {
        return {if $FoldingSubType}'{$FoldingSubType}'{else}null{/if};
    }
    
    {$interfaces}
}

?>