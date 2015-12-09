<?php

/**
 * Утилиты для работы с url
 *
 * @author azazello
 */
final class PsUrl {

    /**
     * Метод проверяет, является ли переданный url - путём http или https.
     * 
     * @param str $url
     * @return bool
     */
    public static function isHttp($url) {
        return $url && (@preg_match('#^http://#', $url) || @preg_match('#^https://#', $url));
    }

    /**
     * Метод приводит переданный URL к адресу для текущего сайта.
     * 
     * PsUrl::toHttp(): http://postupayu.ru/
     * PsUrl::toHttp('path.php?a=b'): http://postupayu.ru/path.php?a=b
     * 
     * @param str $uri - урл адрес, добавляемый после пути
     * @return type
     */
    public static function toHttp($uri = '') {
        if (self::isHttp($uri)) {
            return $uri; //---
        }
        /*
          if (ServerArrayAdapter::IS_SHELL()) {
          return null; //Программа запущена из командной строки
          }
         */
        $protocol = ServerArrayAdapter::PROTOCOL();

        $host = ServerArrayAdapter::HTTP_HOST();
        $host = $host ? $host : '127.0.0.1';

        $port = ServerArrayAdapter::SERVER_PORT();
        $port = !$port || $port == 80 || contains_substring($host, ':') ? '' : ":$port";

        $uri = str_replace('\\', '/', trim($uri));
        $uri = $uri ? ensure_starts_with($uri, '/') : '';

        return "$protocol://$host$port$uri";
    }

    /**
     * Метод возвращает текущий открытый url:
     * http://postupayu.ru/t.php?a=b
     */
    public static function current() {
        return self::toHttp(ServerArrayAdapter::REQUEST_URI());
    }

    /**
     * Метод преобразует строку или массив get-параметров в строку: a=b&c=d.
     * 
     * @param array|string $params
     * @return string
     */
    public static function getParamsToString($params) {
        $tokens = array();
        if (is_array($params)) {
            foreach ($params as $key => $value) {
                $key = trim($key);
                if ($key) {
                    $tokens[] = $key . '=' . trim($value);
                }
            }
        } else {
            $tokens[] = trim($params);
        }
        return implode('&', $tokens);
    }

    /**
     * Метод к URL добавляет параметры и якорь
     * 
     * @param str $base - базовый URL
     * @param array|str $params - строка параметров
     * @param str $sub - якорь
     * @return str
     */
    public static function addParams($base, $params, $sub = null) {
        $base = trim($base);
        $params = self::getParamsToString($params);
        $delimiter = $base && $params ? (contains_substring($base, '?') ? (ends_with($base, '?') ? '' : '&') : '?') : '';
        $sub = $sub ? ensure_starts_with($sub, '#') : '';
        return $base . $delimiter . $params . $sub;
    }

}

?>