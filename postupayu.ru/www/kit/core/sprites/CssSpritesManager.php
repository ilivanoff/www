<?php

class CssSpritesManager {
    /** Директории */

    const DIR_ICO = 'ico';
    const DIR_HEADER = 'header';

    /**
     * Метод возвращает спрайты для всех зарегистрированных директорий
     */
    public static function getAllDirsSptites() {
        $sprites = array();
        foreach (PsUtil::getClassConsts(__CLASS__, 'DIR_') as $dirName) {
            $sprites[$dirName] = self::getSprite($dirName);
        }
        return $sprites;
    }

    /** @return CssSprite */
    public static function getSprite($item) {
        return CssSprite::inst($item);
    }

    /**
     * Спрайт для формулы
     */
    public static function getFormulaSprite(Spritable $item, $formula, $classes = null) {
        $itemName = TexTools::formulaHash($formula);
        $atts = array();
        $atts['data']['tex'] = $itemName;
        $atts['class'][] = $classes;
        return self::getSprite($item)->getSpriteSpan($itemName, $atts);
    }

    /**
     * Спрайт для картинки
     */
    public static function getDirSprite($dir, $itemName, $withGray = false) {
        return self::getSprite($dir)->getSpriteSpan($itemName, array(), $withGray);
    }

}

?>