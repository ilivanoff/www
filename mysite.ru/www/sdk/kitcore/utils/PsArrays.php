<?php

/**
 * Статический класс для хранения методов по работе с массивами.
 *
 * @author azazello
 */
class PsArrays {

    /**
     * Метод "разворачивает" массив, делая его плоским (все вложенные массивы будут также развёрнуты)
     * 
     * @param type $array - массив
     * @param type $takeAll - признак взятия всех элементов или только не пустых
     * @return array
     */
    public static function expand($array, $takeAll = false) {
        $result = array();
        self::expand0($array, $result, $takeAll);
        return $result;
    }

    private static function expand0($var, &$result, $takeAll) {
        if (is_array($var)) {
            foreach ($var as $value) {
                self::expand0($value, $result, $takeAll);
            }
        } else {
            if ($takeAll || !isTotallyEmpty($var)) {
                $result[] = $var;
            }
        }
    }

}

?>
