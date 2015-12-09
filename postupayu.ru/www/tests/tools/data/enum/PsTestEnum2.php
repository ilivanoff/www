<?php

/**
 * Description of PsTestEnum
 *
 * @author azazello
 */
class PsTestEnum2 extends PsTestEnum {

    public static final function ARR() {
        return self::inst(array(1, 2, 3));
    }

    public static final function FLOAT() {
        return self::inst(1.2);
    }

    public static function isARR(PsTestEnum $enum) {
        return $enum === self::ARR();
    }

}

?>