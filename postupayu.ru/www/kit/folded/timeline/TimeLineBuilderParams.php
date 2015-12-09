<?php

/**
 * Параметры построения хронологической шкалы
 *
 * @author azazello
 */
final class TimeLineBuilderParams {

    /** Возможные параметры для построения композиции хронологической шкалы */
    private $compositionExpectedParams;

    function __construct($compositionExpectedParams = null) {
        $this->compositionExpectedParams = to_array($compositionExpectedParams);
    }

    public function getCompositionExpectedParams() {
        return $this->compositionExpectedParams;
    }

}

?>