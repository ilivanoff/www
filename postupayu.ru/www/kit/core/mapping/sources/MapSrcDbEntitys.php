<?php

/**
 * Все сущности БД системы ( таблицы, представления)
 *
 * @author azazello
 */
class MapSrcDbEntitys extends MappingSource {

    protected function init($mident, array $params) {
        
    }

    private $ENTITYS;

    protected function preload($mident, array $params) {
        $this->ENTITYS = DbBean::inst()->getAllTablesAndViews();
    }

    protected function loadDescription($mident, array $params) {
        return 'Таблицы и представления базы';
    }

    protected function loadIdentsLeft($mident, array $params) {
        return $this->ENTITYS;
    }

    protected function loadIdentsRight($mident, array $params, \MappingSource $srcLeft, $lident) {
        return $this->ENTITYS;
    }

}

?>
