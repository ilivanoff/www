<?php

/**
 * Класс для работы с кешем на уровне БД. Будет автоматически сброшен при 
 * переключении на другую базу.
 *
 * @author azazello
 */
final class PsDbCahce {

    private static $CACHES = array();

    /**
     * Метод возвращает экземпляр кеша для класса
     * 
     * @param string название класса
     * @return SimpleDataCache
     */
    public static function getCache($class) {
        if (array_key_exists($class, self::$CACHES)) {
            return self::$CACHES[$class];
        }

        PsLogger::inst(__CLASS__)->info('+ Cache for [{}]', PsCheck::notEmptyString($class));

        return self::$CACHES[$class] = new SimpleDataCache();
    }

    /**
     * Метод очищает кеш по указанному классу
     * 
     * @param string название класса
     */
    public static function clear($class) {
        if (array_key_exists($class, self::$CACHES)) {
            PsLogger::inst(__CLASS__)->info('~ Clear for [{}]', $class);
            self::$CACHES[$class]->clear();
        }
    }

    /**
     * Очищает все кеши
     */
    public static function clearAll() {
        PsLogger::inst(__CLASS__)->info('! Clear all db caches');
        foreach (self::$CACHES as $class => $cache) {
            self::clear($class);
        }
    }

}

?>
