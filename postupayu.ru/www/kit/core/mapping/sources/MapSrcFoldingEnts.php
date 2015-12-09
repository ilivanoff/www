<?php

/**
 * Все сущности фолдингов системы
 *
 * @author azazello
 */
class MapSrcFoldingEnts extends MappingSource {

    /** @var FoldedResources */
    private $folding;

    protected function init($mident, array $params) {
        $this->folding = Handlers::getInstance()->getFoldingByUnique($params['unique']);
    }

    protected function preload($mident, array $params) {
        
    }

    protected function loadIdentsLeft($mident, array $params) {
        return $this->folding->getAccessibleIdents();
    }

    protected function loadIdentsRight($mident, array $params, MappingSource $cfgLeft, $identLeft) {
        $allIdents = $this->folding->getAccessibleIdents();
        switch ($mident) {
            case 'RUBRIC_2_SCCONTROLLERS':
                if ($this->folding instanceof ShowcasesCtrlManager) {
                    return array_diff($allIdents, $this->folding->getBaseControllerIdents());
                }
        }
        return $allIdents;
    }

    protected function loadDescription($mident, array $params) {
        return '' . $this->folding;
    }

    public function getFolding() {
        return $this->folding;
    }

}

?>