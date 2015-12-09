<?php

/**
 *
 * @author azazello
 */
class TL_pattern extends TimeLineBuilderBase {

    public function getTitle() {
        return 'Название шкалы';
    }

    protected function getTimeLineBuilderParams() {
        return new TimeLineBuilderParams();
    }

    protected function buildComposition(ArrayAdapter $params) {
        /*
         * Построение композиции хронологической шкалы
         */
        return new TimeLineItemsComposite();
    }

    protected function buildPresentation($ident, ArrayAdapter $params) {
        /*
         * Построение представления элемента хронологической шкалы
         */
        return $ident;
    }

}

?>