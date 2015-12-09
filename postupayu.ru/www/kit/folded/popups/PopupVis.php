<?php

/**
 *
 * Константы для popup
 */
class PopupVis {
    /**
     * ТИПЫ ВИДИМОСТИ POPUP-страниц
     */

    const TRUE = 1;         //Виден для popup
    const TRUE_DEFAULT = 2; //Виден и является деволтным
    const FALSE = 3;        //Скрыт
    const BYPOST = 4;       //Определяется постом (доступен только для плагинов, встречаемость popup страниц в постах мы не отслеживаем)

    public static function isAllwaysVisible($type) {
        switch ($type) {
            case self::TRUE:
            case self::TRUE_DEFAULT:
                return true;
        }
        return false;
    }

    public static function isCanBeVisible($type) {
        return self::isAllwaysVisible($type) || self::BYPOST === $type;
    }

}

?>
