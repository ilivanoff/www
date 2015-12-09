<?php

/**
 * Все фолдинги системы
 *
 * @author azazello
 */
class MapSrcAllFoldings extends MappingSource {

    protected function init($mident, array $params) {
        
    }

    private $UNIQUES;

    protected function preload($mident, array $params) {
        $this->UNIQUES = array_keys(Handlers::getInstance()->getFoldingsIndexed());
    }

    protected function loadDescription($mident, array $params) {
        return 'Все фолдинги системы';
    }

    protected function loadIdentsLeft($mident, array $params) {
        return $this->UNIQUES;
    }

    protected function loadIdentsRight($mident, array $params, \MappingSource $srcLeft, $lident) {
        return $this->UNIQUES;
    }

}

?>
