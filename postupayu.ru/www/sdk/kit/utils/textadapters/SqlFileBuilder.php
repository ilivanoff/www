<?php

/**
 * Класс для построения SQL файлов.
 */
class SqlFileBuilder extends AbstractDirItemAdapter {

    protected function onInit(DirItem $di) {
        //do nothing
    }

    /**
     * Очищает содержимое файла
     * 
     * @return SqlFileBuilder
     */
    public function clean() {
        $this->di->remove()->touch();
        return $this;
    }

    public function appendMlComment($comments) {
        $comments = to_array($comments);
        if (empty($comments)) {
            return; //---
        }

        $this->di->writeLineToFile();
        $this->di->writeLineToFile('/*');
        foreach ($comments as $comment) {
            $comment = trim($comment);
            if ($comment) {
                $this->di->writeLineToFile(" * $comment");
            }
        }
        $this->di->writeLineToFile(' */');
    }

    public function appendLine($line = '') {
        $this->di->writeLineToFile($line);
    }

    public function appendFile(DirItem $file, $ensure = true) {
        $contents = trim($file->getFileContents($ensure));
        if ($contents) {
            $contents = remove_utf8_bom($contents);
            $this->appendMlComment("+ FILE [{$file->getName()}]");
            $this->di->writeLineToFile($contents);
        }
        unset($contents);
    }

    public function save() {
        //ПЕРЕЗАПИШЕМ В ПРАВИЛЬНОЙ КОДИРОВКЕ
        $ctt = $this->di->touch()->getFileContents();
        $this->di->writeToFile(mb_convert_encoding(trim($ctt), 'UTF-8', mb_detect_encoding($ctt)), true);
    }

}

?>