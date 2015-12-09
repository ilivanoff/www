<?php

/**
 * Ресурсы всплывающих подсказок
 *
 * @author azazello
 */
abstract class HelpResources extends FoldedResources {

    protected $RESOURCE_TYPES_ALLOWED = array(self::RTYPE_TPL);

    //Предпросмотр сущности фолдинга при редактировании
    public function getFoldedEntityPreview($ident) {
        return array('info' => PsBubble::spanFoldedEntityBubble($ident, $this->getUnique($ident)), 'content' => $this->fetchTplImpl($ident));
    }

    protected function isIncludeToList($ident, $list) {
        
    }

    protected function onEntityChangedImpl($ident) {
        
    }

    public function getEntityName() {
        return 'Подсказка';
    }

    public function getFoldingType() {
        return 'help';
    }

    public function getFoldingSubType() {
        return null;
    }

    public function getFoldingGroup() {
        return 'help';
    }

}

?>
