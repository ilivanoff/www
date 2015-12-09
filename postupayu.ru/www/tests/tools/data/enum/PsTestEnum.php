<?php

/**
 * Description of PsTestEnum
 *
 * @author azazello
 */
class PsTestEnum extends PsEnum {

    private $value;

    public static final function INT() {
        return self::inst(1);
    }

    public static final function STRING() {
        return self::inst(__FUNCTION__);
    }

    public static final function BOOLEAN() {
        return self::inst(true);
    }

    public static final function DOUBLE() {
        return self::inst(1.1);
    }

    public static function isINT(PsTestEnum $enum) {
        return $enum === self::INT();
    }

    public static function getByName($name) {
        return self::valueOf($name);
    }

    protected function init($default = null) {
        $this->value = $default;
    }

    public function getValue() {
        return $this->value;
    }

}

?>