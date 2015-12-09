<?php

/**
 * Интерфейс для тех контекстов, в рамках которых проходит нумерация формул
 */
interface ImageNumeratorContext {
    /**
     * Класс, который проставляется контейнеру, внутри которого все картинки 
     * должны иметь сквозную нумерацию.
     * То есть во время фетчинга сущности мы проставляем картинкам номера, сообразно их номеру
     * в шаблоне. Но позднее, при отображении на странице эти номера могут быть пересчитаны.
     */

    const CSS_NUMERATOR_IMG = PsConstJs::CSS_NUMERATOR_IMG;

    public function wrapBlockImgBox($imageId, array $attrs, $content);

    public function getBlockImgHref($imageId);
}

?>