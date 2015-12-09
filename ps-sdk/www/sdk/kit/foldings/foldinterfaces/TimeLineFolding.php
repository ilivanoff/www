<?php

/**
 * Базовый интерфейс для тех фолдингов, которые могут отображать свои элементы на хронологической шкале.
 * 
 * @author azazello
 */
interface TimeLineFolding {

    /**
     * @return TimeLineBuilderParams - параметры построения хронологической шкалы
     */
    public function getTimeLineBuilderParams();

    /**
     * 
     * @param TimeLineItemsComposite - композиция элементов хронологической шкалы
     */
    public function buildTimeLineComposition(ArrayAdapter $params);

    /**
     * @param type $ident - идентификатор элемента хронологической шкалы
     * @param ArrayAdapter $params - параметры построения хронологической шкалы
     */
    public function buildTimeLineItemPresentation($ident, ArrayAdapter $params);
}

?>