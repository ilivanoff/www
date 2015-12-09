<?php

class TextFileAdapter extends AbstractDirItemAdapter {

    public $lines;

    protected function onInit(DirItem $di) {
        $this->lines = to_array($di->getFileLines(false));
    }

    public function getLines() {
        return $this->lines;
    }

    /**
     * Массив - строка к кол-ву повторений
     */
    public function lineGroups() {
        $groups = array();
        foreach ($this->lines as $line) {
            $groups[$line] = array_get_value($line, $groups, 0);
            ++$groups[$line];
        }
        return $groups;
    }

    public function hasLine($line) {
        return in_array($line, $this->lines);
    }

}

?>