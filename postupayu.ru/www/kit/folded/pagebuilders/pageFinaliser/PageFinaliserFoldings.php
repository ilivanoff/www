<?php

class PageFinaliserFoldings extends AbstractPageFinalizer {

    protected function doFinalize($CONTENT) {
        //1. Менеджеры, которые могут финализировать страницы, производят это
        /* @var $folding PageFinalizerFolding */
        foreach (Handlers::getInstance()->getPageFinaliseFoldings() as $folding) {
            $CONTENT = $folding->finalizePageContent($CONTENT);
        }
        //2. BubbledFolding добавляют свои ресурсы для быстрого открытия подсказок
        return $CONTENT . $this->getBubbles($CONTENT);
    }

    /**
     * Метод производит поиск ссылок на всплывающие баблы и извлекает массив:
     * идентификатор_бабла => <div>Содержимое бабла</div>
     */
    private function getBubbles($CONTENT) {
        $items = array();
        $this->extractUsedBubbleItems($CONTENT, $items);

        $has = count($items) > 0;

        if ($this->LOGGER->isEnabled()) {
            $this->LOGGER->info();
            $this->LOGGER->info('Элементы всплывающих подсказок, добавленные на страницу:');
            if ($has) {
                foreach ($items as $unique => $bubble) {
                    $this->LOGGER->info("\t$unique");
                }
            } else {
                $this->LOGGER->info("\t-- Нет --");
            }
        }

        return $has ? PsHtml::div(array('id' => PsConstJs::BUBBLE_LOADER_FOLDING_STORE_ID), implode('', $items)) : '';
    }

    private function extractUsedBubbleItems($content, array &$items = array()) {
        $matches = array();
        $data = PsConstJs::BUBBLE_LOADER_FOLDING_DATA;
        $pattern = "/data-$data=\"(.+?)\"/si";
        preg_match_all($pattern, $content, $matches);

        $uniques = array_diff(array_unique(array_get_value(1, $matches, array())), array_keys($items));
        //Сначала соберём все баблы
        foreach ($uniques as $unique) {
            $bubble = PsBubble::extractFoldedEntityBubbleDiv($unique);
            if ($bubble) {
                $items[$unique] = $bubble;
            }
        }
        //Соберём баблы, вложенные в другие баблы
        foreach ($uniques as $unique) {
            $this->extractUsedBubbleItems($items[$unique], $items);
        }
    }

}

?>