<?php

/**
 * Вспомогательные методы для работы с codemirror
 */
class PsCodemirror {

    private static $remap = array(
        'php' => 'application/x-httpd-php',
        'js' => 'javascript',
        'html' => 'htmlmixed',
        'xml' => 'application/xml'
    );

    public static function checkType($type) {
        return array_get_value($type, self::$remap, $type);
    }

}

?>