<?php

class DatesTools {

    private $LOGGER;

    /*
     * Типы приведения к строке
     */

    const TS_MONTH_FULL = 1; /*  [23 апреля 1858г] */
    const TS_MONTH_SHORT = 2; /* [23 апр 1858г] */
    const TS_NUMBERED = 3; /*    [23.04.1858г] */

    public function isValidDate($date) {
        return $this->getDateAdapter($date) instanceof DateAdapter;
    }

    public function assertValidDate($date, $entityDescr = null) {
        check_condition($this->isValidDate($date), "Невалидный формат даты: '$date'" . ($entityDescr ? " для '$entityDescr'" : ""));
    }

    /** @return DateAdapter */
    public function getDateAdapterUTS($time) {
        return $this->getDateAdapter($this->uts2dateInCurTZ($time, DF_MYSQL));
    }

    /** @return DateAdapter */
    public function getDateAdapter($date) {
        if ($date instanceof DateAdapter) {

            if (!$this->cache->has($date->date())) {
                $this->LOGGER->info('Not found in cache ' . $date);
                if ($date->isInvalid()) {
                    return null;
                }
                $this->cache->set($date->date(), $date);
            } else {
                $this->LOGGER->info('Found in cache ' . $date);
            }

            return $this->cache->get($date->date());
        } else {
            $date = trim($date);

            if (!$this->cache->has($date)) {
                $this->LOGGER->info('Not found in cache ' . $date);
                $dateAdpt = new DateAdapter($date);
                if ($dateAdpt->isInvalid()) {
                    return null;
                }
                $this->cache->set($date, $dateAdpt);
            } else {
                $this->LOGGER->info('Found in cache ' . $date);
            }

            return $this->cache->get($date);
        }
    }

    private function monthDecodes($m) {
        $m = @(int) $m;
        switch ($m) {
            case 1:
                return array('Jan', 'янв', 'January', 'января');
            case 2:
                return array('Feb', 'февр', 'February', 'февраля');
            case 3:
                return array('Mar', 'марта', 'March', 'марта');
            case 4:
                return array('Apr', 'апр', 'April', 'апреля');
            case 5:
                return array('May', 'мая', 'May', 'мая');
            case 6:
                return array('Jun', 'июня', 'June', 'июня');
            case 7:
                return array('Jul', 'июля', 'July', 'июля');
            case 8:
                return array('Aug', 'авг', 'August', 'августа');
            case 9:
                return array('Sep', 'сент', 'September', 'сентября');
            case 10:
                return array('Oct', 'окт', 'October', 'октября');
            case 11:
                return array('Nov', 'ноя', 'November', 'ноября');
            case 12:
                return array('Dec', 'дек', 'December', 'декабря');
            default:
                return array(null, "мес[$m]", null, "месяц[$m]");
        }
    }

    private function decodeMonth($m, $type) {
        $dec = $this->monthDecodes($m);
        switch ($type) {
            case self::TS_MONTH_SHORT:
                return $dec[1];
            case self::TS_MONTH_FULL:
                return $dec[3];
        }
    }

    public function shortMonth($m) {
        return value_Array(1, $this->monthDecodes($m));
    }

    public function fullMonth($m) {
        return value_Array(3, $this->monthDecodes($m));
    }

    public function localize($date, $format = null) {
        if (!$format || contains_substring($format, 'F') || contains_substring($format, 'M')) {
            for ($index = 1; $index <= 12; $index++) {
                $decodes = $this->monthDecodes($index);
                $shortEng = $decodes[0];
                if (contains_substring($date, $shortEng)) {
                    $shortRus = $decodes[1];
                    $longEng = $decodes[2];
                    $longRus = $decodes[3];

                    $date = str_replace($longEng, $longRus, $date);
                    $date = str_replace($shortEng, $shortRus, $date);
                    return $date;
                }
            }
        }
        return $date;
    }

    /*
     * 
     * МЕТОДЫ ДЛЯ РАБОТЫ С ДАТАМИ
     * 
     */

    private function toStringImpl($type, $date) {
        if (isEmpty($date)) {
            return null;
        }

        /* @var $dateAdapter DateAdapter */
        $dateAdapter = $this->getDateAdapter($date);

        if (!$dateAdapter) {
            return $date;
        }

        if ($dateAdapter->isBC()) {
            return $dateAdapter->y() . 'г. до н.э.';
        } else {
            $d = $dateAdapter->d();
            $m = $dateAdapter->m();
            $y = $dateAdapter->y();

            switch ($type) {
                case self::TS_MONTH_FULL:
                case self::TS_MONTH_SHORT:
                    $mt = $this->decodeMonth($m, $type);
                    return "$d $mt $y" . 'г.';
                case self::TS_NUMBERED:
                    $d = pad_zero_left($d, 2);
                    $m = pad_zero_left($m, 2);
                    return "$d.$m.$y" . 'г.';
            }
        }
    }

    public function toString($type, $dateFrom, $dateTo = null) {
        $tsFrom = $this->toStringImpl($type, $dateFrom);
        $tsTo = $tsFrom ? $this->toStringImpl($type, $dateTo) : null;

        $result = $tsFrom ? ($tsFrom . ($tsTo ? ' &mdash; ' . $tsTo : '')) : null;
        return $result ? "<span class=\"nowrap\">$result</span>" : '';
    }

    public function toStringUTS($type, $utsFrom, $utsTo = null) {
        $tsFrom = $utsFrom ? $this->toStringImpl($type, $this->uts2dateInCurTZ($utsFrom)) : null;
        $tsTo = $tsFrom && $utsTo ? $this->toStringImpl($type, $this->uts2dateInCurTZ($utsTo)) : null;

        $result = $tsFrom ? ($tsFrom . ($tsTo ? ' &mdash; ' . $tsTo : '')) : null;
        return $result ? "<span class=\"nowrap\">$result</span>" : '';
    }

    public function uts2dateInCurTZ($time, $format = DF_MYSQL) {
        check_condition(is_numeric($time), "Bad uts [$time] given for method " . __FUNCTION__);

        $tz = PsTimeZone::inst()->getCurrentDateTimeZone();
        $d = DateTime::createFromFormat('U', $time);
        $d->setTimezone($tz);
        $rormatted = $d->format($format);
        return $this->localize($rormatted, $format);
    }

    public function date2utsInCurTZ($date) {
        check_condition(!isEmpty($date), 'Bad date giwen for method ' . __FUNCTION__);

        $tz = PsTimeZone::inst()->getCurrentDateTimeZone();
        $date = new DateTime($date, $tz);
        return $date->format('U');
    }

    public function parseSeconds($sec) {
        $secFull = abs($sec);
        $minFull = floor($secFull / 60);
        $hourFull = floor($minFull / 60);
        $days = floor($hourFull / 24);

        $sec = $secFull - $minFull * 60;
        $min = $minFull - $hourFull * 60;
        $hour = $hourFull - $days * 24;

        return array(
            'd' => $days,
            'h' => $hour,
            'hf' => $hourFull, //Полное отступление в часах
            'm' => $min,
            'mf' => $minFull, //Полное отступление в минутах
            's' => $sec
        );
    }

    public function formatCurDate($format = DF_MYSQL) {
        return date($format);
    }

    /*
     * 
     * Синглтон
     * 
     */

    private $cache;
    private static $instance = NULL;

    /** @return DatesTools */
    public static function inst() {
        if (self::$instance == NULL) {
            self::$instance = new DatesTools();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->cache = new SimpleDataCache();
        $this->LOGGER = PsLogger::inst(__CLASS__);
    }

}

?>
