<?php

/*
 * Класс работает с корневыми директориями проекта.
 * 
 * PsLogger::inst(__CLASS__) - используем потому, что в конструкторе PsLogger 
 * используется DirManager
 */

class DirManager {

    private $relPath;
    private $absPath;

    //resources
    public static function resources($notCkeckDirs = null, $dirs = null) {
        return self::instShifted('resources', $notCkeckDirs, $dirs);
    }

    //images
    public static function images($subDirs = null) {
        return self::instShifted('resources/images', $subDirs);
    }

    //icons
    public static function icons($subDirs = null) {
        return self::instShifted('resources/images/icons', $subDirs);
    }

    //sprites
    public static function sprites($notCkeckDirs = null, $dirs = null) {
        return self::instShifted('resources/sprites', $notCkeckDirs, $dirs);
    }

    //stuff
    public static function stuff($notCkeckDirs = null, $dirs = null) {
        return self::instShifted('stuff/', $notCkeckDirs, $dirs);
    }

    //stuff
    public static function formules($notCkeckDirs = null, $dirs = null) {
        return self::instShifted('stuff/formules', $notCkeckDirs, $dirs);
    }

    //mmedia
    public static function mmedia($notCkeckDirs = null, $dirs = null) {
        return self::instShifted('mmedia/', $notCkeckDirs, $dirs);
    }

    /** @return DirManager */
    public static function gallery($subDirs = null) {
        return self::mmedia(array('gallery', $subDirs));
    }

    //smarty
    public static function smarty($notCkeckDirs = null, $dirs = null) {
        return self::instShifted('stuff/smarty/', $notCkeckDirs, $dirs);
    }

    //uploads
    public static function uploads($dirs = null) {
        return self::instShifted('stuff', null, array('upload', $dirs));
    }

    //database
    public static function database($notCkeckDirs = null, $dirs = null) {
        return self::instShifted('database', $notCkeckDirs, $dirs);
    }

    //autogen
    public static function autogen($dirs = null) {
        return self::inst(null, array('autogen', $dirs));
    }

    /**
     * Фабрика экземпляров
     */
    private static $insts = array();

    /*
     * $notCkeckDirs - директории, существование которых проверяться не будет
     * $dirs - директории, существование которых будет проверено при создании менеджера
     */

    /** @return DirManager */
    public static function inst($notCkeckDirs = null, $dirs = null) {
        $dirPath = next_level_dir($notCkeckDirs);
        $corePath = normalize_path(PATH_BASE_DIR);

        $isAbs = starts_with($dirPath, $corePath);

        $absPathNotCheck = $isAbs ? $dirPath : next_level_dir($corePath, $dirPath);
        $absPathNotCheck = ensure_dir_endswith_dir_separator($absPathNotCheck);

        $absPath = next_level_dir($absPathNotCheck, $dirs);
        $absPath = ensure_dir_endswith_dir_separator($absPath);

        //Проверим, нужно ли создать структуру директорий
        if ($absPathNotCheck != $absPath && !is_dir($absPath)) {
            @mkdir($absPath, 0777, true);
        }

        if (array_key_exists($absPath, self::$insts)) {
            return self::$insts[$absPath];
        }

        $relPath = cut_string_start($absPath, $corePath);
        $relPath = ensure_dir_startswith_dir_separator($relPath);

        return self::$insts[$absPath] = new DirManager($relPath, $absPath);
    }

    protected static final function instShifted($initial, $notCkeckDirs, $dirs = null) {
        $notCheck[] = $initial;
        $notCheck[] = $notCkeckDirs;
        return self::inst($notCheck, $dirs);
    }

    private function __construct($relPath, $absPath) {
        $this->relPath = $relPath;
        $this->absPath = $absPath;
    }

    /*
     * МЕТОДЫ
     */

    /** @return DirManager */
    public final function cd($notCkeckDirs = null, $dirs = null) {
        $newDm = self::inst(array($this->absPath, $notCkeckDirs), $dirs);
        if ($this->absPath != $newDm->absPath) {
            unset(self::$insts[$this->absPath]);
            $this->relPath = $newDm->relPath;
            $this->absPath = $newDm->absPath;
        }
        return $this;
    }

    /**
     * Метод добавляет хешированные директории к пути
     * 
     * @param type $dirs - директории "перед" хешированными директориями
     * @param type $hashBase - база для хеша. Если это md5, то он и будет использован для построения пути.
     * @return array - массив директорий, составляющих путь к папке
     */
    private function addHashFolders($dirs = null, $hashBase = null) {
        $hash = PsCheck::isMd5($hashBase) ? $hashBase : md5($hashBase ? $hashBase : PsRand::string());
        $dirs = to_array($dirs);
        $dirs[] = 'f' . $hash[0];
        $dirs[] = 'f' . $hash[1];
        $dirs[] = 'f' . $hash[2];
        return $dirs;
    }

    /**
     * Данный метод строит путь относительно текущей директории для нормального распределения файлов
     * и переходит данным менеджером в неё. Пути будут иметь 
     */
    public final function cdToHashFolder($notCkeckDirs = null, $dirs = null, $hashBase = null) {
        return $this->cd($notCkeckDirs, $this->addHashFolders($dirs, $hashBase));
    }

    /** @return DirItem */
    public final function getHashedDirItem($dirs = null, $hashBase = null, $file = null, $ext = null) {
        return $this->getDirItem($this->addHashFolders($dirs, $hashBase), $file, $ext);
    }

    public final function relDirPath($dirs = null) {
        return next_level_dir($this->relPath, $dirs);
    }

    public final function httpFilePath($dirs, $fileName, $ext = null) {
        return PsUrl::toHttp($this->relFilePath($dirs, $fileName, $ext));
    }

    public final function relFilePath($dirs, $fileName, $ext = null) {
        return file_path($this->relDirPath($dirs), $fileName, $ext);
    }

    public final function absDirPath($dirs = null) {
        return next_level_dir($this->absPath, $dirs);
    }

    public final function absFilePath($dirs, $fileName, $ext = null) {
        return file_path($this->absDirPath($dirs), $fileName, $ext);
    }

    public final function isFile($dirs, $fileName, $ext = false) {
        return is_file($this->absFilePath($dirs, $fileName, $ext));
    }

    public final function isDir($dirs = null) {
        return is_dir($this->absDirPath($dirs));
    }

    public final function isImage($dirs, $fileName, $ext = false) {
        return PsImg::isImg($this->absFilePath($dirs, $fileName, $ext));
    }

    /** @return DirItem */
    public final function getDirItem($dirs = null, $file = null, $ext = null) {
        return DirItem::inst($this->absFilePath($dirs, $file, $ext));
    }

    /** @return DirItem */
    public final function touch($dirs = null, $file = null, $ext = null) {
        return $this->getDirItem($dirs, $file, $ext)->touch();
    }

    /**
     * Очистка директории
     * 
     * @param type $dirs - путь к директории для учистки
     * @param type $rmDir - признак удаления самой директории
     * @return DirManager 
     */
    public final function clearDir($dirs = null, $rmDir = false) {
        $subDirNames = $this->getDirContent($dirs, DirItemFilter::DIRS, self::DC_NAMES);

        /* @var $sibDirName DirItem */
        foreach ($subDirNames as $sibDirName) {
            $this->clearDir(array($dirs, $sibDirName), true);
        }

        $files = $this->getDirContent($dirs, DirItemFilter::FILES);
        /* @var $file DirItem */
        foreach ($files as $file) {
            $file->remove();
        }

        if ($rmDir) {
            $this->getDirItem($dirs)->remove();
        }

        return $this;
    }

    /**
     * Тип получения содержимого директории
     */

    const DC_MAP = 1; // Карта: название элемента->DirItem
    const DC_NAMES = 2; // Только названия файлов
    const DC_NAMES_NO_EXT = 3; // Только названия файлов без расширения

    /**
     * Получает содержимое указанной директории
     */

    public final function getDirContent($dirs = null, $filterName = null, $type = self::DC_MAP) {
        $result = array();

        $absDirPath = $this->absDirPath($dirs);
        if (is_dir($absDirPath)) {
            $dir = openDir($absDirPath);
            if ($dir) {
                while ($file = readdir($dir)) {
                    if (!is_valid_file_name($file)) {
                        continue;
                    }

                    $item = $this->getDirItem($dirs, $file);

                    //Эх, как жаль, что нет callback`ов!!!
                    if (!$filterName || DirItemFilter::filter($filterName, $item)) {
                        switch ($type) {
                            case self::DC_MAP:
                                $result[$item->getName()] = $item;
                                break;
                            case self::DC_NAMES:
                                $result[] = $item->getName();
                                break;
                            case self::DC_NAMES_NO_EXT:
                                $result[] = $item->getNameNoExt();
                                break;
                            default:
                                raise_error("Неизвестный тип получения содержимого директории: [$type].");
                                break;
                        }
                    }
                }
                closedir($dir);
            }
        }
        return $result;
    }

    /**
     * Получает кол-во элементов указанной директории
     */
    public final function getDirContentCnt($dirs = null, $filterName = null) {
        $cnt = 0;

        $absDirPath = $this->absDirPath($dirs);
        if (is_dir($absDirPath)) {
            $dir = openDir($absDirPath);
            if ($dir) {
                while ($file = readdir($dir)) {
                    if (!is_valid_file_name($file)) {
                        continue;
                    }
                    if (!$filterName || DirItemFilter::filter($filterName, $this->getDirItem($dirs, $file))) {
                        ++$cnt;
                    }
                }
                closedir($dir);
            }
        }
        return $cnt;
    }

    /**
     * Получает названия папок, вложенных в переданную директорию
     * 
     * @param array $allowed - список допустимых названий подпапок
     */
    public final function getSubDirNames($dirs = null, $allowed = null, $denied = null) {
        $result = array();

        $allowed = $allowed === null ? null : to_array($allowed);
        if (is_array($allowed) && empty($allowed)) {
            return $result;
        }
        $denied = $denied === null ? null : to_array($denied);

        $absDirPath = $this->absDirPath($dirs);
        if (is_dir($absDirPath)) {
            $dir = openDir($absDirPath);
            if ($dir) {
                $absDirPath = ensure_dir_endswith_dir_separator($absDirPath);
                while ($file = readdir($dir)) {
                    if (!is_valid_file_name($file)) {
                        continue;
                    }

                    if (is_array($allowed) && !in_array($file, $allowed)) {
                        continue;
                    }

                    if (is_array($denied) && in_array($file, $denied)) {
                        continue;
                    }

                    if (is_dir($absDirPath . $file)) {
                        $result[] = $file;
                    }
                }
                closedir($dir);
            }
        }
        return $result;
    }

    /**
     * Метод рекурсивно загружает содержимое указанной директории
     * 
     * @param type $dirs - директория-отступ
     * @param type $filterName - фильтр
     * @param type $exceptDirs - исключённые директории, анализ элементов в которой производиться не будет
     * @param type $forceEnableDirs - включённые директории, которые будут форсированно обработаны
     * @return array
     */
    public final function getDirContentFull($dirs = null, $filterName = null, $exceptDirs = array(), $forceEnableDirs = array()) {
        return DirFiller::fill($this->absDirPath($dirs), $filterName, $exceptDirs, $forceEnableDirs);
    }

    /**
     * Метод прокладывает путь до папки, если его небыло
     * 
     * @return DirManager
     */
    public final function makePath($dirs = null) {
        $absPath = $this->absDirPath($dirs);
        if (!is_dir($absPath)) {
            mkdir($absPath, 0777, true);
        }
        return $this;
    }

    public final function copyDirContent2Dir($dirFrom, $dirToAbsPath, $includeDir = true, $filterName = null) {
        $fromDirRelPath = $this->relDirPath($dirFrom);
        $includeDirName = basename($fromDirRelPath);
        check_condition($includeDirName, "Trying to copy root directory");

        $destDirAbsPath = $dirToAbsPath instanceof DirItem ? $dirToAbsPath->getAbsPath() : DirItem::inst($dirToAbsPath)->getAbsPath();
        //$includeDir = $includeDir && $fromDirRelPath && ($fromDirRelPath != DIR_SEPARATOR);

        $content = $this->getDirContentFull($dirFrom, $filterName);

        if (empty($content)) {
            //Папка пуста. Если $includeDir=true, то просто создадим её
            if ($includeDir && $this->isDir($dirFrom)) {
                $destAbsPath = next_level_dir($destDirAbsPath, $includeDirName);
                DirItem::inst($destAbsPath)->makePath();
            }
            return; //---
        }

        /* @var $src DirItem */
        foreach ($content as $src) {
            $relPathNewDirFromOldDir = cut_string_start($src->getRelPath(), $fromDirRelPath);
            $destAbsPath = next_level_dir($destDirAbsPath, $includeDir ? $includeDirName : null, $relPathNewDirFromOldDir);
            $destDi = DirItem::inst($destAbsPath)->makePath();
            if ($src->isFile()) {
                $src->copyTo($destDi);
            }
        }
    }

    public final function __toString() {
        return __CLASS__ . ' [' . $this->relPath . ']';
    }

}

?>