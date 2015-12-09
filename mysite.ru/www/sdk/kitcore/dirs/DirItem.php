<?php

class DirItem implements Spritable {

    private $name;
    private $relPath;
    private $absPath;
    /*
     * Фабрика
     */
    private static $items = array();

    /** @return DirItem */
    public static function inst($path, $name = null, $ext = null) {
        $itemPath = normalize_path(file_path($path, $name, $ext));
        $corePath = normalize_path(PATH_BASE_DIR);

        //Обезопасим пути, в которых есть русские буквы
        try {
            $itemPath = iconv('UTF-8', 'cp1251', $itemPath);
        } catch (Exception $e) {
            //Если произойдёт ошибка - игнорируем
        }

        $isAbs = starts_with($itemPath, $corePath);

        $absPath = $isAbs ? $itemPath : next_level_dir($corePath, $itemPath);

        if (array_key_exists($absPath, self::$items)) {
            return self::$items[$absPath];
        }

        $relPath = cut_string_start($absPath, $corePath);
        $relPath = ensure_starts_with($relPath, DIR_SEPARATOR);

        return self::$items[$absPath] = new DirItem($relPath, $absPath);
    }

    private function __construct($relPath, $absPath) {
        $this->name = basename($absPath);
        $this->relPath = $relPath;
        $this->absPath = $absPath;
    }

    /** @return DirItem */
    public final function getDirItem($dirs = null, $file = null, $ext = null) {
        return self::inst(array($this->absPath, $dirs), $file, $ext);
    }

    /*
     * Пользовательские данные.
     */

    private function assertIsFile($__FUNCTION__, $ensure = true) {
        check_condition(!$ensure || $this->isFile(), "Cannot use {$__FUNCTION__} function on not file {$this}");
    }

    private function assertIsDir($__FUNCTION__, $ensure = true) {
        check_condition(!$ensure || $this->isDir(), "Cannot use {$__FUNCTION__} function on not dir {$this}");
    }

    /**
     * Возвращает DirItem, находящийся на том-же уровне, что и данный элемент
     * 
     * @param str $name
     * @param str $ext
     * @return DirItem
     */
    public function getSibling($name, $ext = null) {
        return DirItem::inst($this->getDirname(), $name, $ext);
    }

    private $data = array();

    /** @return DirItem */
    public function setData($key, $value) {
        $this->data[$key] = $value;
        return $this;
    }

    public function getData($key, $default = '') {
        return array_get_value($key, $this->data, $default);
    }

    public function getName() {
        return $this->name;
    }

    public function getAbsPath() {
        return $this->absPath;
    }

    public function getAbsPathWin() {
        return to_win_path($this->absPath);
    }

    public function getRelPath() {
        return $this->relPath;
    }

    //Относительный путь к файлу, но он не будет начинаться с разделителя директорий
    public function getRelPathNoDs() {
        return cut_string_start($this->relPath, DIR_SEPARATOR);
    }

    public function isImg() {
        return PsImg::isImg($this->absPath);
    }

    public function assertIsImg($text = null) {
        return PsImg::assertIsImg($this->absPath, $text);
    }

    public function isDir() {
        return is_dir($this->absPath);
    }

    public function isFile() {
        return is_file($this->absPath);
    }

    public function getSize() {
        $this->assertIsFile(__FUNCTION__);
        return filesize($this->absPath);
    }

    public function isMaxSize($mbSize) {
        return $this->isFile() && ($this->getSize() > round($mbSize * 1024 * 1024));
    }

    /**
     * Время последней модификации файла.
     * $format = DF_COMMENTS
     * DatesTools - не подключается в kitcore!
     */
    public function getModificationTime($format = null) {
        clearstatcache();
        $time = @filemtime($this->absPath);
        if ($time === false) {
            return null;
        }
        if ($format) {
            return DatesTools::inst()->uts2dateInCurTZ($time, $format);
        }
        return $time;
    }

    //Время последней модификации (в секундах)
    public function getFileLifetime() {
        $mtime = $this->getModificationTime();
        return $mtime ? time() - $mtime : null;
    }

    //TODO - выкинуть или переписать
    public function getMime() {
        $this->assertIsFile(__FUNCTION__);
        $file = $this->absPath;
        if (PsImg::isImg($file)) {
            return array_get_value('mime', getimagesize($file));
        }
        if (function_exists("finfo_file")) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
            $mime = finfo_file($finfo, $file);
            finfo_close($finfo);
            return $mime;
        } else if (function_exists("mime_content_type")) {
            return mime_content_type($file);
        } else if (!stristr(ini_get("disable_functions"), "shell_exec")) {
            // http://stackoverflow.com/a/134930/1593459
            $file = escapeshellarg($file);
            $mime = shell_exec("file -bi " . $file);
            return $mime ? $mime : 'unknown';
        }
        return 'unknown';
    }

    /** @return Array */
    public function getPathInfo() {
        return pathinfo($this->absPath);
    }

    public function hasExtension() {
        return contains_substring($this->name, '.');
    }

    public function getExtension() {
        return pathinfo($this->absPath, PATHINFO_EXTENSION);
    }

    //Путь к директории, в которой находится файл
    public function getDirname() {
        return pathinfo($this->absPath, PATHINFO_DIRNAME);
    }

    public function getNameNoExt() {
        return pathinfo($this->absPath, PATHINFO_FILENAME);
    }

    public function checkExtension($ext) {
        return in_array($this->getExtension(), to_array($ext));
    }

    public function getFileContents($ensure = true, $default = '') {
        $this->assertIsFile(__FUNCTION__, $ensure);
        $content = @file_get_contents($this->absPath);
        return $content === false ? $default : $content;
    }

    private $FILE_CONTENT = null;

    public function getFileContentsCached() {
        if ($this->FILE_CONTENT === null) {
            $this->FILE_CONTENT = $this->getFileContents();
        }
        return $this->FILE_CONTENT;
    }

    //Вычитывает строки файла, пропуская пустые
    public function getFileLines($ensure = true, $takeEmpty = false) {
        $this->assertIsFile(__FUNCTION__, $ensure);
        return @file($this->absPath, $takeEmpty ? 0 : FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    }

    //Метод получает содержимое строки с заданным номером в файле
    public function getFileLine($lineNum) {
        $this->assertIsFile(__FUNCTION__);
        return file_get_line_contents($this->absPath, $lineNum);
    }

    //Вычитывает строки файла, пропуская пустые
    public function getFileAsProps($ensure = true) {
        $lines = $this->getFileLines($ensure);
        $props = array();
        if (is_array($lines)) {
            foreach ($lines as $line) {
                $tokens = explode('=', $line, 2);
                if (count($tokens) == 1) {
                    $props[$tokens[0]] = null;
                }
                if (count($tokens) == 2) {
                    $props[$tokens[0]] = $tokens[1];
                }
            }
        }
        return $props;
    }

    public function parseAsIni($process_sections = true, $ensure = true) {
        return $this->isFile() ? parse_ini_file($this->absPath, $process_sections) : ($ensure ? raise_error('Invalid ini file: ' . $this->relPath) : null);
    }

    /** @return DirItem */
    public function writeToFile($string, $rewrite = false) {
        if ($rewrite) {
            file_put_contents($this->absPath, $string);
        } else {
            if ($string) {
                file_append_contents($this->absPath, $string);
            }
        }
        return $this;
    }

    /** @return DirItem */
    public function putToFile($string) {
        return $this->writeToFile($string, true);
    }

    public function writeLineToFile($string = '', $rewrite = false) {
        $this->writeToFile($string . "\n", $rewrite);
    }

    /** @return DirItem */
    public function remove() {
        //Файл
        if ($this->isFile()) {
            unlink($this->absPath);
            return $this; //---
        }

        //Директория
        if ($this->isDir()) {
            @rmdir($this->absPath);
        }
        return $this;
    }

    public function copyTo($destPath) {
        $destPath = $destPath instanceof DirItem ? $destPath->getAbsPath() : $destPath;
        @copy($this->absPath, $destPath);
    }

    public function removeIfMaxSize() {
        if ($this->isMaxSize()) {
            $this->remove();
        }
    }

    /** @return ZipArchive */
    public function startZip() {
        $zip = new ZipArchive();
        $res = $zip->open($this->absPath, ZipArchive::OVERWRITE);
        check_condition($res === true, 'Failed to open zip 4 create, err code: ' . $res);
        return $zip;
    }

    /** @return ZipArchive */
    public function loadZip() {
        $this->assertIsFile(__FUNCTION__);
        $zip = new ZipArchive();
        $res = $zip->open($this->absPath);
        check_condition($res === true, 'Failed to open zip 4 read, err code: ' . $res);
        return $zip;
    }

    /** @return ZipArchive */
    public function extractZipTo($dirPath) {
        $dirPath = $dirPath instanceof DirItem ? $dirPath->getAbsPath() : $dirPath;
        check_condition(is_dir($dirPath), 'Bad destination dir for zip extract');

        $zip = $this->loadZip();
        $zip->extractTo($dirPath);
        $zip->close();
    }

    /** @return DirItem */
    public function touch($doTouch = true) {
        if ($doTouch) {
            touch($this->absPath);
        }
        return $this;
    }

    /** @return DirItem */
    public function touchIfNotFile($doTouch = true) {
        if ($doTouch && !$this->isFile()) {
            $this->touch(true);
        }
        return $this;
    }

    /** @return DirItem */
    public function makePath() {
        $path = $this->hasExtension() ? $this->getDirname() : $this->absPath;
        @mkdir($path, 0777, true);
        return $this;
    }

    /*
     * Адаптеры
     */

    private $ADAPTERS = array();

    private function getAdapterImpl($className, $doCache = true) {
        if ($doCache && array_key_exists($className, $this->ADAPTERS)) {
            return $this->ADAPTERS[$className];
        }

        check_condition(class_exists($className), "Cannot create $className adapter, class is not loaded.");
        check_condition(is_subclass_of($className, 'AbstractDirItemAdapter'), "Adapter $className is not subclass of AbstractDirItemAdapter.");

        $adapter = new $className($this);
        return $doCache ? $this->ADAPTERS[$className] = $adapter : $adapter;
    }

    /** @return TextFileAdapter */
    public function getTextFileAdapter() {
        return $this->getAdapterImpl('TextFileAdapter');
    }

    /** @return SqlFileBuilder */
    public function getSqlFileBuilder() {
        return $this->getAdapterImpl('SqlFileBuilder');
    }

    /** @return ImageAdapter */
    public function getImageAdapter() {
        return $this->getAdapterImpl('ImageAdapter');
    }

    /** @return TextPropsAdapter */
    public function getTextPropsAdapter() {
        return $this->getAdapterImpl('TextPropsAdapter');
    }

    /*
     * Сериализация/десериализация массивов
     */

    public function saveArrayToFile(array $data) {
        $this->putToFile(serialize($data));
    }

    public function getArrayFromFile() {
        $data = $this->getFileContents(false);
        $data = $data ? @unserialize($data) : null;
        return is_array($data) ? $data : null;
    }

    public function __toString() {
        return __CLASS__ . ' [' . $this->relPath . ']';
    }

    public function getSpriteImages() {
        return DirManager::inst()->getDirContent($this->relPath, DirItemFilter::IMAGES);
    }

    public function getSpriteName() {
        return unique_from_path('dir', $this->relPath);
    }

    public function equals($di) {
        return ($di instanceof DirItem) && ($this->absPath === $di->absPath);
    }

}

?>