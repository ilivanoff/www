<?php

class ImageAdapter extends AbstractDirItemAdapter {

    private $info;
    private $width;
    private $height;

    protected function onInit(DirItem $di) {
        $this->info = getimagesize($di->getAbsPath());
        check_condition($this->info, "В ImageAdapter передана невалидная картинка [{$di->getRelPath()}].");

        $this->width = $this->info[0];
        $this->height = $this->info[1];
    }

    public function getWidth() {
        return $this->width;
    }

    public function getMimeType() {
        return $this->info['mime'];
    }

    public function getMimeBasename() {
        return basename($this->info['mime']);
    }

    /*
     * Возвращает высоту картинки при переданной длине
     */

    public function getHeight($width = null) {
        if (!$width || $width == $this->width) {
            return $this->height;
        } else {
            return round($this->height * $width / $this->width);
        }
    }

}

?>
