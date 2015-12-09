<?php

/*
 * [580 BC]
 * [1858-04-23]
 * [23.04.1858]
 */

class DateAdapter {
    //Возможные типы даты (состояния адаптера)

    const TYPE_BC = 1;
    const TYPE_INVALID = -1;
    const TYPE_VALID = 10;

    //Начальные установки
    private $date = null;
    private $type = self::TYPE_INVALID;

    /* День, месяц, год */
    private $d;
    private $m;
    private $y;

    /* Час, минута, секунда */
    private $hh;
    private $mm;
    private $ss;

    /* Вес для сортировки + формат Y-m-d */
    private $weight;
    private $Ymd;

    function __construct($date) {
        $date = trim($date);

        if (!$date) {
            return; //Невалидная дата
        }

        $this->date = $date;

        //Дата - до Н.Э. (570 BC)
        if (ends_with($date, ' BC')) {
            $tmp = explode(' ', $date);
            $y = count($tmp) == 2 ? $tmp[0] : null;
            if (is_numeric($y)) {
                $this->y = (int) $y;
                $this->type = self::TYPE_BC;
                $this->weight = -$this->y;
                $this->Ymd = $date;
            }
            return; //---
        }

        //Дальше действовать будет date_parse:)
        $tmp = date_parse($date);
        $error_count = array_get_value('error_count', $tmp, 0);
        $warning_count = array_get_value('warning_count', $tmp, 0);
        if ($error_count || $warning_count) {
            return; //Невалидная дата
        }

        //Валидная дата
        $this->y = (int) $tmp['year'];
        $this->m = (int) $tmp['month'];
        $this->d = (int) $tmp['day'];

        $this->hh = (int) $tmp['hour'];
        $this->mm = (int) $tmp['minute'];
        $this->ss = (int) $tmp['second'];

        $y = pad_zero_left($this->y, 4);
        $m = pad_zero_left($this->m, 2);
        $d = pad_zero_left($this->d, 2);

        $this->type = self::TYPE_VALID;
        $this->weight = (int) "$y$m$d";
        $this->Ymd = "$y-$m-$d";
    }

    public function isBC() {
        return $this->type == self::TYPE_BC;
    }

    public function isInvalid() {
        return $this->type == self::TYPE_INVALID;
    }

    public function d() {
        return $this->d;
    }

    public function m() {
        return $this->m;
    }

    public function y() {
        return $this->y;
    }

    public function hh() {
        return $this->hh;
    }

    public function mm() {
        return $this->mm;
    }

    public function ss() {
        return $this->ss;
    }

    /**
     * Исходная дата
     */
    public function date() {
        return $this->date;
    }

    /**
     * "Вес" для сортировки
     */
    public function getSortWeight() {
        return $this->weight;
    }

    /**
     * Дата в формате Y-m-d
     */
    public function toYmd() {
        return $this->Ymd;
    }

    public function __toString() {
        return $this->date;
    }

}

?>
