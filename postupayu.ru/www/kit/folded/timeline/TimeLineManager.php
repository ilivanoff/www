<?php

/**
 * Основной класс, отвечающий за построение хронологической шкалы
 */
class TimeLineManager extends TimeLineResources {
    /**
     * Дефолтный размер обложки
     */

    const COVERS_DIM = '64x';

    /** @return TimeLineBuilderBase */
    private function getBuilder($lident) {
        return $this->getEntityClassInst($lident);
    }

    public function getTimeLineJson($lident, ArrayAdapter $params) {
        return $this->getBuilder($lident)->getTimeLineJson($params);
    }

    /** @return TimeLineItemsComposite */
    public function getTimeLineComposition($lident, ArrayAdapter $params) {
        return $this->getBuilder($lident)->getTimeLineComposition($params);
    }

    public function getTimeLineItemPresentation($lident, $eident, ArrayAdapter $params) {
        return $this->getBuilder($lident)->getTimeLineItemPresentation($eident, $params);
    }

    /** @return FoldedEntity */
    public function getFoldedEntity4TimeLineFolding(TimeLineFolding $folding) {
        foreach ($this->getAllUserAcessibleClassInsts() as $inst) {
            if (($inst instanceof TimeLineFoldingBuilder) && ($inst->getTimeLineFolding() === $folding)) {
                return $inst->getFoldedEntity();
            }
        }
        return null;
    }

    /** @return TimeLineManager */
    public static function inst() {
        return parent::inst();
    }

}

?>