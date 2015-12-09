<?php

/**
 * Типы загрузки пользователей
 *
 * @author azazello
 */
final class UserLoadType extends PsEnum {

    /** @return UserLoadType */
    public static final function ALL() {
        return self::inst();
    }

    /** @return UserLoadType */
    public static final function ADMIN() {
        return self::inst('b_admin=1');
    }

    /** @return UserLoadType */
    public static final function CLIENT() {
        return self::inst('b_admin=0');
    }

    /*
     * ПОЛЯ
     */

    private $restriction;

    protected function init($restriction = '') {
        $this->restriction = $restriction;
    }

    public function getRestriction() {
        return $this->restriction;
    }

}

?>
