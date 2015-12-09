<?php

/**
 * Класс для получения всяхих случайных последовательностей
 *
 * @author azazello
 */
final class PsRand {

    private static $CHARS = 'abcdefghijklmnopqrstuvwxyz';
    private static $CHARNUMS = 'abcdefghijklmnopqrstuvwxyz123456789';

    /**
     * Случайное значение true/false
     */
    public static function bool() {
        return rand(0, 1) === 1;
    }

    /**
     * Случайный символ
     * 
     * @param bool|null $upper - признак работы с uppercase.
     * @param bool $nums - можно ли использовать цифры
     * @return string
     */
    public static function char($upper = null, $nums = false) {
        if ($nums) {
            $char = self::$CHARNUMS[rand(0, strlen(self::$CHARNUMS) - 1)];
        } else {
            $char = self::$CHARS[rand(0, strlen(self::$CHARS) - 1)];
        }
        return $upper === true || ($upper === null && self::bool()) ? strtoupper($char) : $char;
    }

    /**
     * Случайная последовательность символов
     * 
     * @param int $length - длина строки
     * @param bool|null $upper - признак работы с uppercase.
     * @param bool $nums - можно ли использовать цифры
     * @return string
     */
    public static function string($length = MD5_STR_LENGTH, $upper = false, $nums = true) {
        if ($length <= 0) {
            return '';
        }
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= self::char($upper, $nums);
        }
        return $result;
    }

}

?>