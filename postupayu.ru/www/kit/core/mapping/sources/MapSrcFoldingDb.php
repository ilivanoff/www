<?php

/**
 * Фолдинги, хранимые в базе
 *
 * @author azazello
 */
class MapSrcFoldingDb extends MappingSource {

    /** @var FoldedResources */
    private $folding;

    protected function init($mident, array $params) {
        $this->folding = Handlers::getInstance()->getFoldingByUnique($params['unique']);
        $this->folding->assertWorkWithTable();
    }

    protected function preload($mident, array $params) {
        
    }

    protected function loadIdentsLeft($mident, array $params) {
        return $this->folding->getAccessibleDbIdents();
    }

    protected function loadIdentsRight($mident, array $params, MappingSource $cfgLeft, $identLeft) {
        return $this->folding->getAccessibleDbIdents();
    }

    protected function loadDescription($mident, array $params) {
        return '' . $this->folding;
    }

    public function getFolding() {
        return $this->folding;
    }

}

?>