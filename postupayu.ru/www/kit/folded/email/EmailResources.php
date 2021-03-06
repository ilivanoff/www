<?php

/**
 * Ресурсы фолдингов для отправки электронных писем
 *
 * @author azazello
 */
abstract class EmailResources extends FoldedResources {

    /** Допустимые типы ресурсов */
    protected $RESOURCE_TYPES_ALLOWED = array(self::RTYPE_PHP, self::RTYPE_TPL);

    protected function isIncludeToList($ident, $list) {
        return false;
    }

    protected function onEntityChangedImpl($ident) {
        
    }

    public function getEntityName() {
        return 'Электронное письмо';
    }

    public function getFoldingType() {
        return 'email';
    }

    public function getFoldingGroup() {
        return 'email';
    }

    public function getFoldingSubType() {
        return null;
    }

}

?>
