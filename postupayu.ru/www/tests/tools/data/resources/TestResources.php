<?php

/**
 * Description of TestResources
 *
 * @author azazello
 */
class TestResources {

    public static function imageGif() {
        return self::abs('image.gif');
    }

    public static function imageJpg() {
        return self::abs('image.jpg');
    }

    public static function imagePng() {
        return self::abs('image.png');
    }

    public static function fakeimagePng() {
        return self::abs('fakeimage.png');
    }

    private static function abs($name) {
        return __DIR__ . DIR_SEPARATOR . $name;
    }

}

?>
