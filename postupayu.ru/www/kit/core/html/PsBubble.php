<?php

/**
 * Класс, содержащий вспомогательные методы для работы с всплывающими ссылками.
 *
 * @author azazello
 */
class PsBubble {

    //Метод добавляет параметры, которые ассоциируют ссылку с загрузчиком данных по ID. Обычно такие bubble`ы следуют за курсором.
    private static function attrsExtractor(array &$attrs, $boxId, $extractor, $type = PsConstJs::BUBBLE_HREF_MOVE_CLASS) {
        $attrs['class'] = to_array(array_get_value('class', $attrs));
        $attrs['class'][] = $type;
        if ($extractor) {
            //Если не задан, на клиенте будет взят dflt
            $attrs['data']['extractor'] = $extractor;
        }
        $attrs['data']['bubble'] = $boxId;
    }

    //Метод добавляет параметры, которые ассоциируют ссылку с загрузчиком данных по типу. Обычно такие bubble`ы отображаются на краю ссылки.
    private static function attrsLoader(array &$attrs, $loader, $type = PsConstJs::BUBBLE_HREF_STICK_CLASS) {
        $attrs['class'] = to_array(array_get_value('class', $attrs));
        $attrs['class'][] = $type;
        $attrs['data']['loader'] = $loader;
    }

    /**
     * ===============
     * = ИЗВЛЕКАТЕЛИ =
     * ===============
     */

    /**
     * Href-ссылка для отображения содержимого элемента с id=$boxId.
     * 
     * @param type $extractor - название javascript-класса, занимающегося извлеченеим данных из элемента с id=$boxId
     * 
     * <a class="ps-bubblehref-move" data-extractor="theorem" href="#post-tr-vectors-thkoll">№1</a>
     */
    public static function aById($boxId, $content, $extractor = null, array $attrs = array(), $blank = false) {
        self::attrsExtractor($attrs, $boxId, $extractor);

        if (!array_key_exists('href', $attrs)) {
            //Не передана ссылка, подставим в качестве якоря код элемента.
            $blank = false;
            $attrs['href'] = ensure_starts_with($boxId, '#');
        }

        return PsHtml::a($attrs, $content, $blank);
    }

    /**
     * Спан-ссылка для отображения содержимого элемента с id=$boxId.
     * 
     * <span class="ps-bubblehref-move" data-extractor="formula" data-bubble="post-tr-vectors-f6X1">(1)</span>
     */
    public static function spanById($boxId, $content, $extractor = null, array $attrs = array()) {
        self::attrsExtractor($attrs, $boxId, $extractor);
        return PsHtml::span($attrs, $content);
    }

    /**
     * ==============
     * = ЗАГРУЗЧИКИ =
     * ==============
     */

    /**
     * Спан-ссылка для отображения картинки во всплывающем bubble.
     * <span class="ps-bubblehref-move preview" data-loader="image" data-img="/resources/images/author.jpg">Автор</span>
     */
    public static function spanImgBubble($content, $src, array $attrs = array()) {
        self::attrsLoader($attrs, PsConstJs::BUBBLE_LOADER_IMAGE, PsConstJs::BUBBLE_HREF_MOVE_CLASS);
        $attrs['data']['img'] = $src;
        $attrs['class'][] = 'preview';
        return PsHtml::span($attrs, $content);
    }

    /**
     * Лупа, при наведении на которую отображается загружаемая картинка
     * <img class="ps-bubblehref-move preview" src="/resources/images/icons/preview.png" data-loader="image" data-img="/resources/images/logotip.png" alt="preview.png">
     */
    public static function previewImgBubble($src, array $attrs = array()) {
        self::attrsLoader($attrs, PsConstJs::BUBBLE_LOADER_IMAGE, PsConstJs::BUBBLE_HREF_MOVE_CLASS);
        $attrs['data']['img'] = $src;
        $attrs['class'][] = 'preview';

        $attrs['src'] = PsConstJs::IMG_PREVIEW;
        echo PsHtml::img($attrs);
    }

    /**
     * Спан-ссылка для отображения картинки во всплывающем bubble.
     * <span data-folded-item-unique="lib-s-einstein" data-loader="folding" class="ps-bubblehref-stick folded">Эйнштейн</span>
     */
    public static function spanFoldedEntityBubble($content, $unique, array $attrs = array()) {
        self::attrsLoader($attrs, PsConstJs::BUBBLE_LOADER_FOLDING);
        $attrs['class'] = to_array(array_get_value('class', $attrs));
        $attrs['class'][] = 'folded';
        $attrs['data'][PsConstJs::BUBBLE_LOADER_FOLDING_DATA] = $unique;
        return PsHtml::span($attrs, $content);
    }

    /**
     * Див-обёртка для хранения содержимого bubble.
     * <div class="lib-s-BP_FM2_einstein lib-bubble">...</div>
     * И баблы, показываемые на странице, и баблы, загружаемые через ajax - все отображаются в этой обёртке.
     */
    public static function extractFoldedEntityBubbleDiv($unique) {
        $entity = Handlers::getInstance()->getFoldedEntityByUnique($unique, false);
        if (!$entity || !($entity->getFolding() instanceof BubbledFolding)) {
            return null;
        }

        //Добавим классы для контейнера bubble, чтобы мы могли его найти на странице
        $params['class'][] = $entity->getUnique();
        $params['class'][] = $entity->getFolding()->getFoldingType() . '-bubble';

        $bubble = $entity->getFolding()->getBubble($entity->getIdent());
        $clear = PsHtml::div(array('class' => 'clearall'));

        return PsHtml::div($params, $bubble . $clear);
    }

}

?>