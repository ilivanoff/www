<?php

/**
 * Элемент, пригодный для отображения во временной шкале
 */
final class TimeLineItem {

    const DT_NOW = 'now';

    /*
     * Цветовая схема события
     */
    const COLOR_SCHEMA_RED = 'red';
    const COLOR_SCHEMA_GREEN = 'green';
    const COLOR_SCHEMA_BLUE = 'blue';
    /*
     * Иконки
     */
    const ICON_GRAY = 'gray-circle.png';
    const ICON_RED = 'red-circle.png';
    const ICON_GREEN = 'green-circle.png';
    const ICON_BLUE = 'dull-blue-circle.png';

    /*
     * Поля конструктора
     */

    private $title;     //Название события
    private $ident;     //Идентификатор события
    private $dt_start;  //Начало события
    private $dt_stop;   //Окончание события (null|дата|DT_NOW).
    private $interval;  //Признак - нужно ли показывать событие, как интервал. Определяется на основе dt_stop. Для интервала - дата или DT_NOW.
    private $dates;     //Текстовое приедставление временного интервала

    /*
     * Устанавливаемые поля
     */
    private $link;             //url, который будет показан в информации. К нему в любом случае будет применён PsUrl::toHttp().
    private $image;            //Путь к обложке (относительный)
    private $content;          //Содержимое, которое будет показано в всплывающей подсказке
    private $colorSchema;      //Цветовая схема
    private $colorSchemaDflt;  //Признак цветовой схемы по умолчанию

    /*
     * Скрытые поля
     */
    private $icon;        //Иконка события (для interval=0) self::ICON_RED
    private $custom;      //Дополнительная информация для объекта временной шкалы (может быть использована на клиенте)
    private $textColor;   //Цвет текста информации
    private $lineColor;   //Цвет полоски (для interval=1)

    public static function inst($title, $ident, $dt_start, $dt_stop = null) {
        return new TimeLineItem($title, $ident, $dt_start, $dt_stop);
    }

    private function __construct($title, $ident, $dt_start, $dt_stop = null) {
        $dt_start = $this->parseDate($title, $dt_start);
        check_condition($ident, "Не передан идентификатор события '$title'");
        check_condition($dt_start, "Не передана дата начала события для '$title'");
        $dt_stop = $this->parseDate($title, $dt_stop);

        /*
         * При совпадении начала и конца - событие будет отмечено точкой
         */
        if ($dt_start == $dt_stop) {
            $dt_stop = null;
        }

        /*
         * Устанавливаем значения полей
         */
        $this->title = $title;
        $this->ident = $ident;
        $this->dt_start = $dt_start;
        $this->dt_stop = $dt_stop;
        $this->interval = !!$dt_stop;

        /*
         * Высчитываем текстовое представление интервала дат
         * Если дата окончания не задана, то используем полный формат, иначе - сокращённый
         */
        $format = $this->interval ? DatesTools::TS_MONTH_SHORT : DatesTools::TS_MONTH_FULL;
        $this->dates = DatesTools::inst()->toString($format, $this->dt_start, $this->dt_stop);

        /*
         * Инициализируем архив для хранения кастомных данных
         */
        $this->custom = array();

        /*
         * Установим цветовую схему по умолчанию
         */
        $this->setColorSchema(self::COLOR_SCHEMA_BLUE);
        $this->colorSchemaDflt = true;
    }

    /**
     * Преобразует дату в формат для временной шкалы: Y-m-d
     */
    private function parseDate($title, $date) {
        $date = trim($date);
        if (!$date) {
            return null;
        }
        if ($date == self::DT_NOW) {
            return date('Y-m-d');
        }
        $adapter = DatesTools::inst()->getDateAdapter($date);
        check_condition($adapter, "Невалидный формат даты: '$date' для '$title'");
        return $adapter->toYmd();
    }

    /**
     * Цветовая схема. Мы сами установим необходимый цвет события (иконки или полоски) и цвет текста к нему.
     */
    public function setColorSchema($schema) {
        switch ($schema) {
            case self::COLOR_SCHEMA_RED:
                if ($this->interval) {
                    //TODO
                    $this->lineColor = '#a00';
                    $this->textColor = '#a00';
                } else {
                    $this->icon = self::ICON_RED;
                    $this->textColor = '#900';
                }
                break;

            case self::COLOR_SCHEMA_GREEN:
                if ($this->interval) {
                    $this->lineColor = '#0a0';
                    $this->textColor = '#050';
                } else {
                    $this->icon = self::ICON_GREEN;
                    $this->textColor = '#050';
                }
                break;

            case self::COLOR_SCHEMA_BLUE:
                if ($this->interval) {
                    $this->lineColor = null; //По умолчанию
                    $this->textColor = '#005';
                } else {
                    $this->icon = self::ICON_BLUE;
                    $this->textColor = '#33a';
                }
                break;

            default:
                check_condition(false, "Unknown color schema [$schema] for TimeLineItem.");
                break;
        }
        $this->colorSchema = $schema;
        $this->colorSchemaDflt = false;
    }

    public function isDefaultColorSchema() {
        return $this->colorSchemaDflt;
    }

    /**
     * Конструкторные
     */
    public function getTitle() {
        return $this->title;
    }

    public function isInterval() {
        return $this->interval;
    }

    public function getDates() {
        return $this->dates;
    }

    /**
     * Устанавливаемые
     */
    public function getImage() {
        return $this->image;
    }

    public function setImage($image) {
        if ($image instanceof DirItem) {
            $this->image = $image->getRelPath();
        } else {
            $this->image = $image;
        }
    }

    public function setLink($link) {
        $this->link = PsUrl::toHttp($link);
    }

    public function setContent($content) {
        $this->content = $content;
    }

    /**
     * Кастомные данные
     */
    public function addCustom($key, $val) {
        $this->custom[$key] = $val;
    }

    /**
     * "Вес" для сортировки событий
     */
    public function getSortWeight() {
        return DatesTools::inst()->getDateAdapter($this->dt_start)->getSortWeight();
    }

    /**
     * Преобразование в массив.
     * Возвращает события для отображения на временнОй шкале. Параметры:
     * 
     * [title]       - название события
     * [start]       - начало события, в формате 1985-01-29 или 580 BC
     * [end]         - окончание события, в формате 1985-01-29 ил 500 BC
     * [image]       - картинка события
     * [color]       - цвет полоски
     * [link]        - ссылка
     * [textColor]   - цвет текста
     * [icon]        - иконка для события-точки
     * [description] - текст, показываемый под событием
     * 
     */
    public function toArray() {
        $rec = array();
        $rec['title'] = $this->title;
        $rec['start'] = $this->dt_start;
        $rec['image'] = $this->image;
        $rec['textColor'] = $this->textColor;

        /*
         * Все необходимые проверки мы выполняем в классе на момент установки значений, поэтому здесь можем просто собирать установленные значения
         */

        if ($this->link) {
            $rec['link'] = $this->link;
        }

        if ($this->dt_stop) {
            $rec['end'] = $this->dt_stop;
        }

        if ($this->lineColor) {
            $rec['color'] = $this->lineColor;
        }

        if ($this->icon) {
            $rec['icon'] = DirManager::images()->relFilePath('timeline', $this->icon);
        }

        /* Содержимое */
        $rec['description'] = PsHtml::p(array('class' => 'dates'), $this->dates) . $this->content;

        /* Наши данные */
        $this->custom['ident'] = $this->ident;
        $this->custom['interval'] = DatesTools::inst()->toString(DatesTools::TS_NUMBERED, $this->dt_start, $this->dt_stop);

        /* Специфические данные */
        $rec['custom'] = $this->custom;

        return $rec;
    }

}

?>