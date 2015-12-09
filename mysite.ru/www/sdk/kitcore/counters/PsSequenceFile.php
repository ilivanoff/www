<?php

/**
 * Сиквенс, основанный на файлах
 *
 * @author azazello
 */
final class PsSequenceFile extends AbstractSequence {

    /** @var DirItem */
    private $di;

    /** Минимальное значение */
    private $min;

    /** Саксимальное значение */
    private $max;

    /** Шаг */
    private $step;

    private function __construct(DirItem $di, $min, $max, $step) {
        $this->di = $di;
        $this->min = PsCheck::int($min);
        $this->max = PsCheck::int($max);
        $this->step = PsCheck::int($step);
        //Проверки
        check_condition($this->max > $this->min, "Некорректные границы [$this->min, $this->max] для $this");
        check_condition($this->step > 0, "Некорректный шаг [$this->step] для $this");
    }

    public function next() {
        $num = $this->di->getFileContents(false);
        $num = is_inumeric($num) ? 1 * $num + $this->step : null;
        $num = is_integer($num) && ($num <= $this->max) ? $num : $this->min;
        $this->di->putToFile($num);
        return $num;
    }

    public function current() {
        $num = $this->di->getFileContents(false);
        return is_inumeric($num) ? 1 * $num : null;
    }

    /*
     * Синглтон
     */

    private static $insts = array();

    /** @return AbstractSequence */
    public static function inst(DirItem $di, $min = 1, $max = 999999999, $step = 1) {
        return array_key_exists($di->getRelPath(), self::$insts) ? self::$insts[$di->getRelPath()] : self::$insts[$di->getRelPath()] = new PsSequenceFile($di, $min, $max, $step);
    }

    public function __toString() {
        return __CLASS__ . " [{$this->di->getRelPath()}]";
    }

}

?>