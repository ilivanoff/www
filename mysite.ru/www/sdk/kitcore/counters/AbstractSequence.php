<?php

/**
 * Базовый класс для всех сиквенсов
 *
 * @author azazello
 */
abstract class AbstractSequence {

    abstract function next();

    abstract function current();

    public final function hasCurrent() {
        return is_integer($this->current());
    }

    public final function isCurrent($num) {
        return is_inumeric($num) && (1 * $num == $this->current());
    }

}

?>