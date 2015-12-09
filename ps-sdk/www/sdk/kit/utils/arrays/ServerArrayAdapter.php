<?php

final class ServerArrayAdapter extends ArrayAdapter {

    /** @return ServerArrayAdapter */
    public static function inst() {
        return parent::inst($_SERVER, false, false);
    }

    //Загрузка констант
    private static function CONST_STR($__FUNCTION__) {
        return self::inst()->str($__FUNCTION__);
    }

    private static function CONST_INT($__FUNCTION__) {
        return self::inst()->int($__FUNCTION__);
    }

    /*
     * --== КОНСТАНТЫ ==--
     */

    //127.0.0.1
    public static function REMOTE_ADDR() {
        return self::CONST_STR(__FUNCTION__);
    }

    //Mozilla/5.0 (Windows NT 6.1; WOW64; rv:26.0) Gecko/20100101 Firefox/26.0
    public static function HTTP_USER_AGENT() {
        return self::CONST_STR(__FUNCTION__);
    }

    //postupayu.ru
    public static function HTTP_HOST() {
        return cut_string_start(lowertrim(basename(self::CONST_STR(__FUNCTION__))), 'www.');
    }

    //blog.php
    public static function PHP_SELF() {
        return basename(self::CONST_STR(__FUNCTION__));
    }

    //80
    public static function SERVER_PORT() {
        return self::CONST_INT(__FUNCTION__);
    }

    // '/t.php?a=b'
    public static function REQUEST_URI() {
        return self::CONST_STR(__FUNCTION__);
    }

    //HTTP_X_REQUESTED_WITH
    private static function HTTP_X_REQUESTED_WITH() {
        return strtolower(self::CONST_STR(__FUNCTION__));
    }

    //Проверка, является ли запрос - ajax
    public static function IS_AJAX() {
        return self::HTTP_X_REQUESTED_WITH() === 'xmlhttprequest';
    }

    //HTTPS
    private static function HTTPS() {
        return strtolower(self::CONST_STR(__FUNCTION__));
    }

    //Проверка, является ли запрос отправленным по https
    public static function IS_HTTPS() {
        return self::HTTPS() === 'on';
    }

    //Возвращает протокол, по которому работает сервер - http или https
    public static function PROTOCOL() {
        return self::IS_HTTPS() ? 'https' : 'http';
    }

    //argv (массив параметров командной строки)
    public static function ARGV() {
        return self::inst()->get(strtolower(__FUNCTION__));
    }

    //argc (кол-во параметров командной строки)
    public static function ARGC() {
        return self::CONST_INT(strtolower(__FUNCTION__));
    }

    //Проверка, запущен ли скрипт из командной строки (Command Line Interface)
    public static function IS_SHELL() {
        return is_array(self::ARGV()) && is_integer(self::ARGC()) && self::ARGC() > 0;
    }

}

?>