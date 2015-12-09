<?php

/**
 * Хранилище элементов временной шкалы {@link TimeLineItem}.
 * Отвечает за хранение событий, их сортировку и преобразование в вид, пригодный для javascript.
 */
class TimeLineItemsComposite {

    /**
     * Элементы временной шкалы
     */
    private $items;

    /**
     * Признак - была ли коллекция отфетчена
     */
    private $fetched = false;

    public function __construct(array $items = array()) {
        $this->items = $items;
    }

    /** @return TimeLineItemsComposite */
    private function fetch() {
        if ($this->fetched || empty($this->items)) {
            return $this; //---
        }
        $this->fetched = true;

        usort($this->items, function(TimeLineItem $ti1, TimeLineItem $ti2) {
                    return $ti1->getSortWeight() > $ti2->getSortWeight() ? 1 : -1;
                });

        return $this; //---
    }

    /**
     * Добавляет элемент во временную шкалу.
     * Позволим передать null, чтобы во внешнем коде не нужно было делать лишних проверок.
     */
    public function addItem(TimeLineItem $item = null) {
        if ($item) {
            $this->items[] = $item;
            $this->fetched = false;
        }
    }

    /**
     * Возвращает сортированную коллекцию
     */
    public function getItems() {
        return $this->fetch()->items;
    }

    /**
     * Расскрашивает элементы по порядку.
     * Не будут расскрашены события-интервалы и те события, для которых цвет назначался специально.
     * 
     * @return TimeLineItemsComposite
     */
    public function colorOneByOne() {
        $cnt = 0;
        /* @var $item TimeLineItem */
        foreach ($this->getItems() as $item) {
            if (!$item->isInterval() || !$item->isDefaultColorSchema()) {
                //Пропускаем не интервал или событие с установленным цветом
                continue;
            }
            $chet = $cnt++ % 2 == 0;
            $item->setColorSchema($chet ? TimeLineItem::COLOR_SCHEMA_BLUE : TimeLineItem::COLOR_SCHEMA_GREEN);
        }
        return $this;
    }

    /**
     * Представление событий для передачи в javascript
     */
    public function getTimeLineJson() {
        $data['events'] = array();
        /* @var $item TimeLineItem */
        foreach ($this->getItems() as $item) {
            $data['events'][] = $item->toArray();
        }
        //$data['dateTimeFormat'] = 'iso8601';
        return $data;
    }

    /**
     * Поддерживаем клонирование.
     * Можно в любой момент склонировать всё хранилище, модифицировать его элементы и построить временнУю шкалу на их основе.
     */
    public function __clone() {
        $this->items = unserialize(serialize($this->items));
    }

}

?>