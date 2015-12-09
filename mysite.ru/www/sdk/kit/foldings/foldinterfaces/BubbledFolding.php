<?php

/**
 * Базовый интерфейс для тех фолдингов, элементы которых можно показывать во всплывающих bubbe`ах.
 * 
 * @author azazello
 */
interface BubbledFolding {

    /**
     * Возвращает содержимое подсказки
     */
    public function getBubble($ident);

    /**
     * Метод возвращает ссылку для показа всплавающей информации.
     * Обычно сводится к вызову:
     * PsBubble::spanFoldedEntityBubble($text, $unique);
     */
    public function getBubbleHref($ident, $text, ArrayAdapter $smartyParams);
}

?>