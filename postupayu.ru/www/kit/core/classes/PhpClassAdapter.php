<?php

/**
 * Класс предоставляет оболочку над ReflectionClass для быстрого доступа к свойствам класса,
 * например - для представления комментария в виде html.
 *
 * @author azazello
 */
final class PhpClassAdapter {

    /** @var ReflectionClass */
    private $rc;

    /** @var DirItem */
    private $di;

    public function __construct($class) {
        $this->rc = PsUtil::newReflectionClass($class);
        $this->di = DirItem::inst(Autoload::inst()->getClassPath($this->rc->name));
    }

    public function getRc() {
        return $this->rc;
    }

    public function getHtmlDescr() {
        return join('<br/>', StringUtils::parseMultiLineComments($this->rc->getDocComment()));
    }

    public function getMethodAdapters($public = true, $static = null, $final = null, $checkOwned = true) {
        $methods = array();
        foreach (PsUtil::getClassMethods($this->rc->name, $public, $static, $final, $checkOwned) as $method) {
            $methods[$method] = PhpMethodAdapter::inst($this->rc->name, $method);
        }
        return $methods;
    }

    public function getDi() {
        return $this->di;
    }

    /**
     * Метод возвращает содержимое php файла, удаляя открывающий и закрывающий php теги
     */
    public function getFileContentsNoTags() {
        return trim(cut_string_end(cut_string_start($this->getDi()->getFileContents(), '<?php'), '?>'));
    }

    /**
     * Возвращает тело класса/интерфейса
     */
    public function getClassBody() {
        $lines = $this->di->getFileLines(true, true);
        $firstLine = $this->rc->getStartLine();
        $endLine = $this->rc->getEndLine();
        if ($endLine <= $firstLine + 1) {
            return '';
        }
        return trim(implode('', array_slice($lines, $firstLine, $endLine - $firstLine - 1)));
    }

    public static function inst($class) {
        return new PhpClassAdapter($class);
    }

}

?>