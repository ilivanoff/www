<?php

/**
 * Расширение DirManager с методами для быстрого допуска к предопределённым директориям
 *
 * @author azazello
 */
class DirManagerSdk extends DirManager {

    const DIR_STORAGE = 'storage';
    const DIR_CONFIG = 'config';
    const DIR_LIB = 'lib';

    /**
     * Путь к базовой папке с файлами SDK
     * 
     * @return DirManager
     */
    public static final function sdk($notCkeckDirs = null) {
        return self::inst(array(SDK_DIR, $notCkeckDirs));
    }

    /**
     * Хранилище - папка, которую можно и коммитить. Она хранит данные,
     * получаемые динамически, но потом много раз используемые. Пример - данные из таблиц БД.
     * 
     * Тем не менее создавать мы такие папки будем при обращении к ним - чтобы нам не
     * приходилось их заводить руками в момент, когда они понадобятся какому-либо 
     * сурвису.
     */
    public static function storage($dirs = null) {
        return self::inst(null, array(self::DIR_STORAGE, $dirs));
    }

    /**
     * Хранилище (см. ранее) для SDK.
     */
    public static function storageSdk($dirs = null) {
        return self::inst(null, array(SDK_DIR, self::DIR_STORAGE, $dirs));
    }

}

?>