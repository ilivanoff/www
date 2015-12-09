<?php

define('PATH_PLUGINS', PATH_BASE_DIR . '/plugins');

/**
 * Класс для подключения внешних плагинов.
 * @author azazello
 */
class ExternalPluginsManager extends ExternalPluginsSdk {

    /**
     * Метод проверит - относится ли файл к файлам внешних плагинов
     */
    public static function isExternalFile($fileAbsPath) {
        return parent::isExternalFile($fileAbsPath) || starts_with(normalize_path($fileAbsPath), normalize_path(PATH_PLUGINS . '/'));
    }

    /**
     * 
     */
    public static function PhpMailer() {
        if (self::isInclude(__FUNCTION__)) {
            require_once PATH_PLUGINS . '/PHPMailer_5.2.4/class.phpmailer.php';
        }
    }

    /**
     * 
     */
    public static function MathEvaluator() {
        if (self::isInclude(__FUNCTION__)) {
            require_once PATH_PLUGINS . '/evalmath/evalmath.class.php';
        }
    }

    /**
     * 
     */
    public static function Smarty() {
        if (self::isInclude(__FUNCTION__)) {
            require_once PATH_PLUGINS . '/Smarty-3.1.21/libs/Smarty.class.php';
        }
    }

    /**
     * 
     */
    public static function GifEncored() {
        if (self::isInclude(__FUNCTION__)) {
            require_once PATH_PLUGINS . '/gifencoder/GIFEncoder.class.php';
        }
    }

    /**
     * 
     */
    public static function Censure() {
        if (self::isInclude(__FUNCTION__)) {
            require_once PATH_PLUGINS . '/Censure-3.2.7/UTF8.php';
            require_once PATH_PLUGINS . '/Censure-3.2.7/ReflectionTypehint.php';
            require_once PATH_PLUGINS . '/Censure-3.2.7/Text/Censure.php';
        }
    }

    /**
     * 
     */
    public static function Pear() {
        if (self::isInclude(__FUNCTION__)) {
            require_once PATH_PLUGINS . '/PEAR-1.9.4/PEAR.php';
        }
    }

    /**
     * 
     */
    public static function CacheLite() {
        if (self::isInclude(__FUNCTION__)) {
            require_once PATH_PLUGINS . '/Cache_Lite-1.7.11/Cache_Lite-1.7.11/Lite.php';
            require_once PATH_PLUGINS . '/Cache_Lite-1.7.11/Cache_Lite-1.7.11/Lite/Output.php';
        }
    }

    /**
     * 
     */
    public static function SimpleHtmlDom() {
        if (self::isInclude(__FUNCTION__)) {
            require_once PATH_PLUGINS . '/simplehtmldom_1_5/simple_html_dom.php';
        }
    }

    /**
     * 
     */
    public static function SpriteGenerator() {
        if (self::isInclude(__FUNCTION__)) {
            require_once PATH_PLUGINS . '/css-sprite-generator-v4.1/includes/ps-css-sprite-gen.inc.php';
        }
    }

}

?>