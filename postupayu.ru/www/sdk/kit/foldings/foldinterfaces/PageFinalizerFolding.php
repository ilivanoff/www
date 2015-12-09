<?php

/**
 * Базовый интерфейс для тех фолдингов, которые могут финализировать контент страницы.
 * 
 * @author azazello
 */
interface PageFinalizerFolding {

    /**
     * Содержимое страницы - то, которое между тегами
     * <body>
     *    $CONTENT
     * </body>
     */
    public function finalizePageContent($CONTENT);
}

?>