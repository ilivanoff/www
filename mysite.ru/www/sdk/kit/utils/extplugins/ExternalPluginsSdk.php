<?php

define('PATH_PLUGINS_SDK', DirManagerSdk::sdk(DirManagerSdk::DIR_LIB)->absDirPath());

/**
 * Класс для подключения внешних php плагинов.
 * @author azazello
 */
class ExternalPluginsSdk {

    /**
     * Метод проверит - относится ли файл к файлам внешних плагинов
     */
    public static function isExternalFile($fileAbsPath) {
        return starts_with(normalize_path($fileAbsPath), PATH_PLUGINS_SDK);
    }

    /**
     * 
     */
    public final static function AdoDb() {
        if (self::isInclude(__FUNCTION__)) {
            require_once PATH_PLUGINS_SDK . 'adodb5/adodb.inc.php';
            require_once PATH_PLUGINS_SDK . 'adodb5/drivers/adodb-mysql.inc.php';

            GLOBAL $ADODB_FETCH_MODE, $ADODB_COUNTRECS;

            $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
            $ADODB_COUNTRECS = false;
        }
    }

    /*
     * 
     * УТИЛИТЫ
     * 
     */

    private static $included = array();

    protected static final function isInclude($key) {
        if (array_key_exists($key, self::$included)) {
            return false;
        }
        self::$included[$key] = true;
        PsLogger::inst(__CLASS__)->info('+ {}', $key);
        return true;
    }

}

?>