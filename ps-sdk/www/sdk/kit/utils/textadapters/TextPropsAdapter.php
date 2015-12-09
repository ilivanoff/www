<?php

class TextPropsAdapter extends AbstractDirItemAdapter {

    private $props;

    public function getProps() {
        if (is_array($this->props)) {
            return $this->props;
        }

        $this->props = array();

        if (!$this->di->isFile()) {
            return $this->props;
        }

        $prop = null;
        $content = array();
        foreach ($this->di->getFileLines() as $line) {
            $line = trim($line);

            if (!$line) {
                continue;
            }

            if (starts_with($line, '[') && ends_with($line, ']')) {
                //Нашли свойство. Приверим, а нет ли информации о предыдущем свойстве
                if ($prop) {
                    $this->props[$prop] = trim(implode("\n", $content));
                    $content = array();
                }

                $prop = trim(cut_string_end(cut_string_start($line, '['), ']'));
                continue;
            }

            if (!$prop) {
                continue;
            }

            $content[] = $line;
        }

        if ($prop) {
            $this->props[$prop] = trim(implode("\n", $content));
        }

        return $this->props;
    }

    public function saveProps(array $props) {
        $content = array();
        foreach ($props as $key => $value) {
            $content[] = "[$key]";
            $content[] = $value;
        }
        $this->di->putToFile(implode("\n", $content));
        $this->props = null;
    }

    public function getProp($param) {
        return array_get_value($param, $this->getProps());
    }

    protected function onInit(DirItem $di) {
        
    }

}

?>