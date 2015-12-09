<?php

/**
 * Утилитные методы для работы с картинками
 */
final class PsImg {

    /**
     * Подерживаемые типы картинок и их маппинг на расширения
     */
    private static $TYPE2EXT = array(
        IMAGETYPE_JPEG => array(PsConst::EXT_JPG, PsConst::EXT_JPEG),
        IMAGETYPE_GIF => PsConst::EXT_GIF,
        IMAGETYPE_PNG => PsConst::EXT_PNG
    );
    private static $TYPES;
    private static $EXTS;
    private static $MIMES;

    /**
     * Все допустимые типы картинок
     * Array (IMAGETYPE_JPEG, IMAGETYPE_GIF, IMAGETYPE_PNG) 
     */
    public static function TYPES() {
        return is_array(self::$TYPES) ? self::$TYPES : self::$TYPES = array_keys(self::$TYPE2EXT);
    }

    /**
     * Является ли тип допустимым
     */
    public static function hasType($type) {
        return is_inumeric($type) && in_array($type, self::TYPES());
    }

    /**
     * Все допустимые расширения картинок
     * Array (jpg, jpeg, gif, png) 
     */
    public static function EXTS() {
        return is_array(self::$EXTS) ? self::$EXTS : self::$EXTS = to_array_expand(self::$TYPE2EXT);
    }

    /**
     * Является ли расширение допустимым
     */
    public static function hasExt($ext) {
        return in_array(lowertrim($ext), self::EXTS());
    }

    /**
     * Все допустимые mime-типы картинок
     * Array (image/jpeg, image/gif, image/png)
     */
    public static function MIMES() {
        if (!is_array(self::$MIMES)) {
            self::$MIMES = array();
            foreach (self::TYPES() as $type) {
                self::$MIMES[] = strtolower(image_type_to_mime_type($type));
            }
        }
        return self::$MIMES;
    }

    /**
     * Является ли mime тип допустимым
     */
    public static function hasMime($mime) {
        return in_array(lowertrim($mime), self::MIMES());
    }

    /**
     * Получает числовой код типа картинки
     * 
     * @param mixed $plain - тип|расширение|mime
     * @return int
     */
    public static function getType($plain) {
        if (PsCheck::isInt($plain)) {
            $temp = 1 * $plain;
            check_condition(in_array($temp, self::TYPES()), "Тип картинок [$plain] запрещён");
            return $temp;
        }
        if (PsCheck::isNotEmptyString($plain)) {
            $temp = lowertrim($plain);
            $byExt = in_array($temp, self::EXTS());
            $byMime = !$byExt && in_array($temp, self::MIMES());
            if ($byExt || $byMime) {
                foreach (self::$TYPE2EXT as $type => $exts) {
                    if ($byExt) {
                        //Поиск по расширениям
                        if (in_array($temp, (array) $exts)) {
                            return $type;
                        }
                    } else {
                        //Поиск по mime типам
                        if ($temp == strtolower(image_type_to_mime_type($type))) {
                            return $type;
                        }
                    }
                }
            }
        }
        raise_error("Не удалось определить тип картинки по идентификатору [$plain], либо картинка онтосится к запрещённым типам.");
    }

    /**
     * Получает расширение
     * 
     * @param mixed $type - тип|расширение|mime
     * @return type
     */
    public static function getExt($type) {
        if (self::hasExt($type)) {
            return lowertrim($type);
        }
        return strtolower(image_type_to_extension(self::getType($type), false));
    }

    /**
     * Получает mime-тип
     * 
     * @param mixed $type - тип|расширение|mime
     * @return type
     */
    public static function getMime($type) {
        if (self::hasMime($type)) {
            return lowertrim($type);
        }
        return strtolower(image_type_to_mime_type(self::getType($type)));
    }

    /**
     * Проверяет, является ли файл по переданному пути - картинкой
     * 
     * @return boolean
     */
    public static function isImg($absPath) {
        $imgSize = @getimagesize($absPath);
        if (!$imgSize) {
            return false;
        }

        $width = $imgSize[0];
        $height = $imgSize[1];
        if ($width <= 0 || $height <= 0) {
            return false;
        }

        return array_get_value('mime', $imgSize) === self::getMime($imgSize[2]);
    }

    /**
     * Метод утверждает, что переданный файл является картинкой
     * 
     * @param type $absPath
     */
    public static function assertIsImg($absPath, $text = null) {
        check_condition(self::isImg($absPath), $text ? $text : "Файл не является картинкой [$absPath]");
    }

}

?>