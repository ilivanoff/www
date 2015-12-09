<?php

/**
 * Фильтер элементов директории.
 */
final class DirItemFilter {

    const ALL = 'ALL';
    const DIRS = 'DIRS';
    const FILES = 'FILES';
    const IMAGES = 'IMAGES';
    const ARCHIVES = 'ARCHIVES';

    /**
     * Массив всех зарегистрированных фильтров
     */
    public static function getFilters() {
        return PsUtil::getClassConsts(__CLASS__);
    }

    public static function filter($type, DirItem $item) {
        if (is_array($type)) {
            foreach ($type as $_type) {
                if (self::filter($_type, $item)) {
                    return true;
                }
            }
        }

        /*
         * Обработаем callback
         */
        if (is_callable($type)) {
            return !!call_user_func($type, $item);
        }

        $type = $type ? $type : self::ALL;

        switch ($type) {
            case self::ALL:
                return true;
            case self::IMAGES:
                return $item->isImg();
            case self::DIRS:
                return $item->isDir();
            case self::FILES:
                return $item->isFile();
            case self::ARCHIVES:
                return $item->checkExtension(array(PsConst::EXT_RAR, PsConst::EXT_ZIP));
            default :
                //Если ни один из фильтров не подошел, проверим, может мы фильтруем по расшерению?
                if (PsConst::hasExt($type)) {
                    return $item->checkExtension($type);
                }
                raise_error("Unknown dir item filter type: [$type].");
        }
    }

}

?>