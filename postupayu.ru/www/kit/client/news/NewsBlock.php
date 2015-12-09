<?php

/**
 * Новостной блок.
 * 
 * Класс создан для удобства хранения в себе информации о событиях, произошедших в один день.
 *
 * @author azazello
 */
class NewsBlock {

    /** События - коллекиця объектов NewsEvent */
    private $events = array();

    /** Дата всего блока [16 февраля 2013г.] */
    private $blockDate;

    /** Дата в формате для datepicker [11-12-2013] */
    private $pickerDate;

    /**
     * Метод добавления события в блок событий.
     * 
     * При этом дата самого событийного блока определяется датой первого добавленного в него события.
     * Все остальные собыия должны иметь ту же дату.
     * 
     * @param NewsEvent $event
     */
    public function addEvent(NewsEvent $event) {
        if (empty($this->events)) {
            $this->blockDate = $event->getBlockDate();
            $this->pickerDate = DatesTools::inst()->uts2dateInCurTZ($event->getNewsEventUtc(), DF_JS_DATEPICKER);
        } else {
            check_condition($this->blockDate == $event->getBlockDate(), "Новость на дату [{$event->getBlockDate()}] не может быть добавлена в блок на дату [{$this->blockDate}]");
        }
        $this->events[] = $event;
    }

    /**
     * Дата новостного блока. Вычисляется один раз на основе даты первого события, добавленного в блок.
     * Все остальные события должны относиться к этой дате.
     */
    public function getBlockDate() {
        return $this->blockDate;
    }

    /**
     * Метод возвращает признак - был ли новостной блок этой даты закеширован.
     */
    public function isCached() {
        return !!PSCache::NEWS()->getFromCache($this->blockDate);
    }

    public function getBlockHtml() {
        $block = PSCache::NEWS()->getFromCache($this->blockDate);
        if (!$block) {
            $block = PSSmarty::template('news/date_news.tpl', array('block' => $this))->fetch();
            PSCache::NEWS()->saveToCache($block, $this->blockDate);
        }
        return $block;
    }

    /**
     * Дата для datepicker
     */
    public function getPickerDate() {
        return $this->pickerDate;
    }

    public function getEvents() {
        return $this->events;
    }

}

?>
