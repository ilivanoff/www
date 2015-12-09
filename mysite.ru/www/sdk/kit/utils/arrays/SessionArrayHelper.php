<?php

/**
 * Класс для поддержки работы с сессией
 *
 * @author azazello
 */
final class SessionArrayHelper {

    public static function reset($key) {
        unset($_SESSION[$key]);
    }

    public static function hasInt($key) {
        return is_integer(array_get_value($key, $_SESSION));
    }

    public static function getInt($key) {
        return self::hasInt($key) ? $_SESSION[$key] : null;
    }

    public static function setInt($key, $int) {
        $_SESSION[$key] = PsCheck::int($int);
    }

}

?>
