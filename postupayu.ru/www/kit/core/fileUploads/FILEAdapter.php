<?php

/**
 * Ксласс для работы с глобальным массивом $_FILE
 *
 * @author azazello
 */
class FILEAdapter {

    /** @var ArrayAdapter */
    private $params;
    private $valid = false;

    public function __construct($mandatory = true, $fileFormParam = FORM_PARAM_FILE) {
        if (!array_key_exists($fileFormParam, $_FILES)) {
            check_condition(!$mandatory, "Ключ [$fileFormParam] не найден в массиве файлов");
            return; //---
        }

        $this->params = ArrayAdapter::inst($_FILES[$fileFormParam]);

        $code = $this->params->int('error');

        if (!$mandatory && $code == UPLOAD_ERR_NO_FILE) {
            return; //---
        }

        check_condition($code == UPLOAD_ERR_OK, $this->getErrorDescription($code));

        $tmpName = $this->params->str('tmp_name');
        check_condition(is_uploaded_file($tmpName), 'Файл не является загруженным');

        $size = $this->params->int('size');
        check_condition(($size > 0) && ($size <= UPLOAD_MAX_FILE_SIZE), "Недопустимый размер загружаемого файла: $size байт.");

        $this->valid = true;
    }

    private function assertIsValid() {
        check_condition($this->valid, 'Загружаемый файл не валиден!');
    }

    public function isValid() {
        return $this->valid;
    }

    public function assertIsImg() {
        $this->assertIsValid();
        PsImg::assertIsImg($this->getTmpFilePath());
    }

    public function getOriginalName() {
        return basename($this->params->str('name'));
    }

    public function getType() {
        return $this->params->str('type');
    }

    public function getTmpFilePath() {
        return $this->params->str('tmp_name');
    }

    /** @return DirItem */
    public function moveUploadedFileTo(DirItem $di) {
        $this->assertIsValid();
        check_condition(move_uploaded_file($this->getTmpFilePath(), $di->getAbsPath()), 'Не удаётся переместить временный файл');
        return $di;
    }

    private function getErrorDescription($code) {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
            case UPLOAD_ERR_FORM_SIZE:
                return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
            case UPLOAD_ERR_PARTIAL:
                return 'The uploaded file was only partially uploaded';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing a temporary folder';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk';
            case UPLOAD_ERR_EXTENSION:
                return 'File upload stopped by extension';
            default:
                return 'Unknown upload error';
        }
    }

    public function __toString() {
        return __CLASS__ . ' ' . array_to_string($this->params->getData(), false);
    }

}

?>