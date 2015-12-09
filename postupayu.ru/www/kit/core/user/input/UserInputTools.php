<?php

/**
 * Утилиты для обработки ввода пользователя.
 */
final class UserInputTools {
    /*
     * 
     * ==============================
     *         КОРОТКИЙ ТЕКСТ
     * ==============================
     * 
     */

    public static function safeShortText($string) {
        check_condition($string, 'Пустое сообщение');
        check_condition(!TexTools::hasTex($string), 'Короткое сообщение не может содержать формулы');
        check_condition(ps_strlen($string) <= 255, 'Текст не должен превышать 255 символов');

        $string = htmlspecialchars($string);
        $string = nl2brr($string);

        return normalize_string($string);
    }

    /*
     * 
     * ==============================
     *         БОЛЬШОЙ ТЕКСТ
     * ==============================
     * 
     */

    public static function safeLongText($string) {
        check_condition($string, 'Пустое сообщение');

        $texExtractor = TexExtractor::inst($string, true);

        $string = $texExtractor->getMaskedText();
        $string = htmlspecialchars($string);
        $string = nl2brr($string);

        $string = $texExtractor->restoreMasks($string);

        /*
         * Удалим двойные пробелы, т.к. к этом моменту уже все переносы 
         * строк заменены на <br />
         */
        return normalize_string($string);
    }

    /*
     * 
     * =============================================================
     *  ПРЕОБРАЗУЕТ ТЕКСТ ДЛЯ ПОВТОРНОГО РЕДАКТИРОВАНИЯ В <textarea>
     * =============================================================
     * 
     */

    public static function unsafeText($string) {
        return htmlspecialchars($string);
    }

}

?>
