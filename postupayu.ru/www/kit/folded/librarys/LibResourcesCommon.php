<?php

/**
 * Класс содержит функциональность, общую для всех библиотек.
 */
class LibResourcesCommon {

    /** @var PsLoggerInterface */
    private $LOGGER;

    /**
     * Функция производит финализацию страницы, показываемой пользователю, добавляя к ней элемент с содержащимися в нём ссылками.
     */
    public function appendLibItemsToPageContent($CONTENT) {
        $matches = array();
        $data = PsConstJs::BUBBLE_LOADER_FOLDING_DATA;
        $pattern = "/data-$data=\"(.+?)\"/si";
        preg_match_all($pattern, $CONTENT, $matches);
        $matches = array_unique(array_get_value(1, $matches, array()));

        $items = array();

        foreach ($matches as $unique) {
            $entity = Handlers::getInstance()->getFoldedEntityByUnique($unique, false);
            if ($entity && ($entity->getFolding()->isItByType(LibResources::LIB_FOLDING_TYPE))) {
                $items[$entity->getUnique()] = $entity->getFolding()->getBubble($entity->getIdent());
            }
        }

        $has = count($items) > 0;

        if ($this->LOGGER->isEnabled()) {
            $this->LOGGER->info();
            $this->LOGGER->info('Библиотечные элементы, добавленные на страницу:');
            if ($has) {
                foreach ($items as $unique => $lib) {
                    $this->LOGGER->info("\t$unique");
                }
            } else {
                $this->LOGGER->info("\t-- Нет --");
            }
        }

        return $CONTENT . ($has ? PsHtml::div(array('id' => PsConstJs::BUBBLE_LOADER_FOLDING_STORE_ID), implode('', $items)) : '');
    }

    /**
     * Метод определяет сущность из библиотеки по переданным параметрам.
     * Может быть задан любой из параметров.
     * 
     * @return FoldedEntity
     */
    public function defineFoldedEntity($type, $ident, $text) {
        $this->LOGGER->info();
        $this->LOGGER->info("Определяем элемент по параметрам: [$type|$ident|$text]");

        if ($type && $ident) {
            $this->LOGGER->info('Быстрый поиск по типу и идентификатору');
            $folding = Handlers::getInstance()->getLibManager($type, false);
            return $folding ? $folding->getFoldedEntity($ident) : null;
        }

        $items = LibBean::inst()->getLibItemsSearchAmong($type, $ident, $text);
        $count = is_array($items) ? count($items) : 0;
        if ($count == 0) {
            $this->LOGGER->info('В базе нет подходящих сущностей');
            return null;
        }

        if ($count == 1) {
            $this->LOGGER->info('В базе найдена одна подходящая сущность: ' . $items[0]['grup'] . '-' . $items[0]['ident'] . ' (' . $items[0]['name'] . ')');
            return Handlers::getInstance()->getLibManager($items[0]['grup'])->getFoldedEntity($items[0]['ident']);
        }

        $this->LOGGER->info('В базе найдено {} подходящих сущностей, начинаем выбор...', $count);

        /*
         * В базе есть несколько сущностей, удовлетворяющих нашим условиям.
         * Мы должны выбрать наиболее близкую по тексту ссылки.
         */

        /**
         * Минимальная длина последовательности общих совпадающих символов, при которой можно считать, 
         * что мы нашли элемент библиотеки по тексту.
         * Она не может быть больше длины текста, заданного в ссылке.
         */
        $takeMinLen = min(5, ps_strlen($text));

        $cnt = 0;
        $lastItem = null;
        $lastItemMaxlen = null;
        $lastItemCommonCharsCnt = null;
        foreach ($items as $item) {
            $nowItemMaxlen = StringUtils::getCommonMaxSequenceLen($text, $item['name']);
            if ($nowItemMaxlen < $takeMinLen) {
                continue;
            }
            if (!$lastItemMaxlen || ($lastItemMaxlen < $nowItemMaxlen)) {
                $cnt = 1;
                $lastItem = $item;
                $lastItemMaxlen = $nowItemMaxlen;
                continue;
            }
            if ($lastItemMaxlen == $nowItemMaxlen) {
                if (!is_numeric($lastItemCommonCharsCnt)) {
                    $lastItemCommonCharsCnt = StringUtils::getCommonCharsCount($text, $lastItem['name']);
                }
                $nowItemCommonCharsCnt = StringUtils::getCommonCharsCount($text, $item['name']);
                if ($nowItemCommonCharsCnt > $lastItemCommonCharsCnt) {
                    //Одинаковая длина общей последовательности букв, но у текущего элемента больше общих букв
                    $lastItem = $item;
                    $lastItemMaxlen = $nowItemMaxlen;
                    $lastItemCommonCharsCnt = $nowItemCommonCharsCnt;
                } else if ($nowItemCommonCharsCnt == $lastItemCommonCharsCnt) {
                    //Одинаковая длина одинаковой последовательности букв и кол-ва общих букв
                    ++$cnt;
                }
            }
        }

        $this->LOGGER->info('По окончанию выборки найдено совпадений: ' . $cnt);

        return $cnt == 1 ? Handlers::getInstance()->getLibManager($lastItem['grup'])->getFoldedEntity($lastItem['ident']) : null;
    }

    /**
     * 
     * Синглтон
     * 
     */
    private static $inst;

    /** @return LibResourcesCommon */
    public static function inst() {
        return self::$inst = isset(self::$inst) ? self::$inst : new LibResourcesCommon();
    }

    private function __construct() {
        $this->LOGGER = PsLogger::inst(get_called_class());
    }

}

?>
