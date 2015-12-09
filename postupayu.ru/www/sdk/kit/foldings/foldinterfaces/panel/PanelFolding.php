<?php

/**
 * Базовый интерфейс для тех фолдингов, которые могут предоставлять панели для встраивания на стрницах.
 * Просто вызываем {'panelName'|trpostpanel} и будет добавлена панель panelName от фолдинга post-tr.
 * 
 * @author azazello
 */
interface PanelFolding {

    /**
     * Метод строит и возвращает панель для отображения на странице.
     * 
     * Может быть возвращён либо null, либо объект типа PluggablePanel.
     * Класс фолдинга для каждой панели должен содержать константу PANEL_.
     * Пример: ClientBoxManager::PANEL_RCOLUMN.
     * 
     * @param string $panelName - название панели
     * @return PluggablePanel - экземпляр панели
     */
    function buildPanel($panelName);
}

?>