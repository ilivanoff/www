<?php

/**
 * Расширение {@see TimeLineBuilderBase} для хронологических шкал,
 * которые строятся на основе других фолдингов.
 */
abstract class TimeLineFoldingBuilder extends TimeLineBuilderBase {

    /** @return TimeLineFolding */
    public abstract function getTimeLineFolding();

    /** @return TimeLineBuilderParams */
    protected function getTimeLineBuilderParams() {
        return $this->getTimeLineFolding()->getTimeLineBuilderParams();
    }

    /** @return TimeLineItemsComposite */
    protected function buildComposition(ArrayAdapter $params) {
        return $this->getTimeLineFolding()->buildTimeLineComposition($params);
    }

    protected function buildPresentation($ident, ArrayAdapter $params) {
        return $this->getTimeLineFolding()->buildTimeLineItemPresentation($ident, $params);
    }

}

?>