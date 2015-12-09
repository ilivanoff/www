<?php

/**
 * Фолдинги для обычных страниц
 *
 * @author azazello
 */
abstract class BasicPagesResources extends FoldedResources {

    //Нам нужен максимальный набор ресурсов
    protected $RESOURCE_TYPES_ALLOWED = array(self::RTYPE_JS, self::RTYPE_CSS, self::RTYPE_PCSS, self::RTYPE_PHP, self::RTYPE_TPL);

    protected function isIncludeToList($ident, $list) {
        
    }

    protected function onEntityChangedImpl($ident) {
        
    }

    public function getEntityName() {
        return 'Базовые страницы';
    }

    public function getFoldingGroup() {
        return 'basics';
    }

    public function getFoldingType() {
        return 'bp';
    }

    public function getFoldingSubType() {
        return null;
    }

}

?>