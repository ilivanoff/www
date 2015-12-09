<?php

class SimpleImage {

    private $path;
    private $type;
    private $image;

    private function __construct() {
        //
    }

    /** @return SimpleImage */
    public static function inst() {
        return new SimpleImage();
    }

    /** @return SimpleImage */
    public function load($path) {
        $this->close();

        $path = $path instanceof DirItem ? $path->getAbsPath() : $path;

        PsImg::assertIsImg($path);

        $this->path = $path;
        $this->type = PsImg::getType(array_get_value(2, getimagesize($path)));
        switch ($this->type) {
            case IMAGETYPE_JPEG:
                $this->image = imagecreatefromjpeg($path);
                break;
            case IMAGETYPE_GIF:
                $this->image = imagecreatefromgif($path);
                break;
            case IMAGETYPE_PNG:
                $this->image = imagecreatefrompng($path);
                break;
        }
        return $this;
    }

    /*
     * Если передать null, то будет создана прозрачаня картинка
     */

    /** @return SimpleImage */
    public function create($w, $h, $fill = 0xFFFFFF) {
        $this->close();
        $this->path = null;
        $this->image = $this->newImg($w, $h, $fill);
        return $this;
    }

    /** @return SimpleImage */
    private function newImg($w, $h, $fill = null) {
        $image = imagecreatetruecolor($w, $h);
        if ($fill === null) {
            imagealphablending($image, false);
            imagesavealpha($image, true);
            $col = imagecolorallocatealpha($image, 255, 255, 255, 127);
            imagefill($image, 0, 0, $col);
        } else {
            imagefill($image, 0, 0, $fill);
        }
        return $image;
    }

    /** @return SimpleImage */
    public function reload() {
        return $this->load($this->path);
    }

    /*
     * ВАЖНО!!!
     * Была обнаружена ошибка - связка imagecreatefrompng+imagepng (вызванные подряд)
     * некорректно выводят картинки, так что вызов метода save() без вызова других методов
     * может привести к битым картинкам! Как минимум нужно вызвать doSave().
     */

    /** @return SimpleImage */
    public function save($path = null, $type = IMAGETYPE_JPEG) {
        $path = $path instanceof DirItem ? $path->getAbsPath() : $path;
        $path = $path ? $path : $this->path;
        check_condition($path, "Invalid path for image save given: [$path]");
        //Проверять расширение вот так нельзя, так как .jpg и .jpeg - не пройдут
        //$path = ensure_file_ext($path, PsImg::getExt($type));
        return $this->output($type, $path);
    }

    /** @return SimpleImage */
    public function output($type = IMAGETYPE_JPEG, $path = null) {
        $code = PsImg::getType($type);
        switch ($code) {
            case IMAGETYPE_JPEG:
                imagejpeg($this->image, $path, 100);
                break;
            case IMAGETYPE_GIF:
                imagegif($this->image, $path);
                break;
            case IMAGETYPE_PNG:
                imagepng($this->image, $path);
                break;
        }
        return $this;
    }

    public function colorAt($x, $y) {
        return imagecolorat($this->image, $x, $y);
    }

    public function getWidth() {
        return imagesx($this->image);
    }

    public function getHeight() {
        return imagesy($this->image);
    }

    /*
     * Вписывает прямоугольник в квадрат.
     */

    /** @return SimpleImage */
    public function resizeToSquare($square_size) {
        $thumb_width = $this->getWidth();
        $thumb_height = $this->getHeight();

        if ($thumb_width > $thumb_height) {
            $this->resizeToWidth($square_size);
        } else {
            $this->resizeToHeight($square_size);
        }

        $thumb_width = $this->getWidth();
        $thumb_height = $this->getHeight();

        $x_dest = 0;
        $y_dest = 0;

        if ($thumb_height < $thumb_width) {
            $y_dest = ($square_size - $thumb_height) / 2;
        } else if ($thumb_height > $thumb_width) {
            $x_dest = ($square_size - $thumb_width) / 2;
        }

        $new_image = $this->newImg($square_size, $square_size);
        imagecopy($new_image, $this->image, $x_dest, $y_dest, 0, 0, $thumb_width, $thumb_height);
        $this->image = $new_image;

        return $this;
    }

    /** @return SimpleImage */
    public function resizeSmart($width = null, $height = null) {
        check_condition($width || $height, 'No width and no height given');

        if (!$width) {
            $this->resizeToHeight($height);
            return $this;
        }

        if (!$height) {
            $this->resizeToWidth($width);
            return $this;
        }

        if ($width == $height) {
            $this->resizeToSquare($width);
        } else {
            $this->resize($width, $height);
        }

        return $this;
    }

    /*
     * Вырезает и вписывает прямоугольник в квадрат.
     */

    public function resizeToSquareCut($square_size) {
        $thumb_width = $this->getWidth();
        $thumb_height = $this->getHeight();

        if ($thumb_width > $thumb_height) {
            $this->resizeToHeight($square_size);
        } else {
            $this->resizeToWidth($square_size);
        }

        $thumb_width = $this->getWidth();
        $thumb_height = $this->getHeight();

        $x_src = 0;
        $y_src = 0;

        if ($thumb_height < $thumb_width) {
// wide
            $x_src = ($thumb_width - $square_size) / 2;
        } else if ($thumb_height > $thumb_width) {
// landscape
            $y_src = ($thumb_height - $square_size) / 2;
        }

        $this->copy($square_size, $square_size, $x_src, $y_src);
        return $this;
    }

    /*
     * Вписывает прямоугольник в квадрат с заданной стороной.
     */

    public function resizeToRectangle($max_size) {
        $thumb_width = $this->getWidth();
        $thumb_height = $this->getHeight();

        if ($thumb_width > $thumb_height) {
            $this->resizeToWidth($max_size);
        } else {
            $this->resizeToHeight($max_size);
        }
        return $this;
    }

    public function resizeToHeight($height) {
        $ratio = $height / $this->getHeight();
        $width = $this->getWidth() * $ratio;
        return $this->resize($width, $height);
    }

    public function resizeToWidth($width) {
        $ratio = $width / $this->getWidth();
        $height = $this->getHeight() * $ratio;
        return $this->resize($width, $height);
    }

    public function scale($scale) {
        $width = $this->getWidth() * $scale / 100;
        $height = $this->getHeight() * $scale / 100;
        $this->resize($width, $height);
        return $this;
    }

    private function resize($width, $height) {
        $new_image = $this->newImg($width, $height);
        imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
        $this->image = $new_image;
        return $this;
    }

    private function copy($width, $height, $x_src = 0, $y_src = 0) {
        $new_image = $this->newImg($width, $height);
        imagecopy($new_image, $this->image, 0, 0, $x_src, $y_src, $width, $height);
        $this->image = $new_image;
        return $this;
    }

    public function copyFromAnother(SimpleImage $another, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h) {
        imagecopy($this->image, $another->image, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h);
        return $this;
    }

    public function close() {
        if (isset($this->image)) {
            @imagedestroy($this->image);
            unset($this->image);
        }
    }

    function __destruct() {
        $this->close();
    }

    public function doSave() {
        $this->copy($this->getWidth(), $this->getHeight());
        return $this;
    }

}

?>
