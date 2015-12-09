<?php

/**
 * Менеджер для работы с элементами таблицы Менделеева
 */
class MendeleevManager extends AbstractSingleton {

    private $ELEMENTS = array(
        array(1, 'H', 'Водород', 1.008),
        array(2, 'He', 'Гелий', 4.003),
        array(3, 'Li', 'Литий', 6.941),
        array(4, 'Be', 'Берилий', 9.0122),
        array(5, 'B', 'Бор', 10.811),
        array(6, 'C', 'Углерод', 12.011),
        array(7, 'N', 'Азот', 14.007),
        array(8, 'O', 'Кислород', 15.999),
        array(9, 'F', 'Фтор', 18.998),
        array(10, 'Ne', 'Неон', 20.179)
    );

    public function getHtml($num) {
        $num = is_numeric($num) ? (int) $num : trim($num);
        foreach ($this->ELEMENTS as $arr) {
            if ($arr[0] === $num || $arr[1] === $num) {
                return $this->getHtmlImpl($arr);
            }
        }
    }

    private function getHtmlImpl(array $el) {
        $data['num'] = $el[0];
        $data['sym'] = $el[1];
        $data['name'] = $el[2];
        $data['mass'] = $el[3];

        //Подсчитаем уровни
        $s1 = 0;
        $s2 = 0;
        $p = 0;
        for ($index = 1; $index <= $data['num']; $index++) {
            if ($s1 < 2) {
                ++$s1;
            } else if ($s2 < 2) {
                ++$s2;
            } else {
                ++$p;
            }
        }

        check_condition($s1 >= 1 && $p <= 8, "For atom energy levels s1=$s1 and p=$p");

        if ($p > 0) {
            $levels[] = "2p<sup>$p</sup>";
        }
        if ($s2 > 0) {
            $levels[] = "2s<sup>$s2</sup>";
        }
        $levels[] = "1s<sup>$s1</sup>";

        $data['levels'] = $levels;

        return PSSmarty::template('common/mend_elem.tpl', $data)->fetch();
    }

    /** @return MendeleevManager */
    public static function getInstance() {
        return self::inst();
    }

}

?>
