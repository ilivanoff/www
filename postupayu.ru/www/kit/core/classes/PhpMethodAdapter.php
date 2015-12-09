<?php

/**
 * Класс предоставляет оболочку над ReflectionMethod для быстрого доступа к свойствам метода,
 * например - для представления комментария в виде html.
 *
 * @author azazello
 */
final class PhpMethodAdapter {

    /** @var ReflectionMethod */
    public $rm;

    public function __construct($class, $method) {
        $this->rm = PsUtil::newReflectionMethod($class, $method);
    }

    public function getHtmlDescr() {
        return join('<br/>', StringUtils::parseMultiLineComments($this->rm->getDocComment()));
    }

    public static function inst($class, $method) {
        return new PhpMethodAdapter($class, $method);
    }

}

?>