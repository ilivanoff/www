<?php

/**
 * Description of DirManagerTestHelper
 *
 * @author azazello
 */
class DirManagerTestHelper {

    const DIR = 'dir';
    const TMP = 'tmp';
    const IMAGES = 'images';
    const SCRAP = 'scrap';

    public static function dirsAbsPath($subDir = null) {
        return next_level_dir(__DIR__, DIRECTORY_SEPARATOR, $subDir, DIRECTORY_SEPARATOR);
    }

    public static function imagesAbsPath() {
        return self::dirsAbsPath(self::IMAGES);
    }

    public static function tmpAbsPath() {
        return self::dirsAbsPath(self::TMP);
    }

    public static function scrapAbsPath() {
        return self::dirsAbsPath(self::SCRAP);
    }

    public static function removeDir($name) {
        DirManager::inst(self::dirsAbsPath($name))->clearDir(null, true);
    }

    public static function imageNames($withExts = true) {
        $result = array();
        for ($i = 0; $i < 4; $i++) {
            $dim = pow(2, 4 + $i);
            $result[] = $dim . 'x' . $dim . ($withExts ? '.png' : '');
        }
        sort($result);
        return $result;
    }

    /**
     * Содержимое папки-свалки
     */
    private static $SCRAP = array(
        'dir1' => array(DirItemFilter::DIRS),
        'dir2' => array(DirItemFilter::DIRS),
        'settings.prop' => array(DirItemFilter::FILES, PsConst::EXT_PROP),
        'info.txt' => array(DirItemFilter::FILES, PsConst::EXT_TXT),
        'script.js' => array(DirItemFilter::FILES, PsConst::EXT_JS),
        'script.sql' => array(DirItemFilter::FILES, PsConst::EXT_SQL),
        'pattern.tpl' => array(DirItemFilter::FILES, PsConst::EXT_TPL),
        'page.php' => array(DirItemFilter::FILES, PsConst::EXT_PHP),
        'style.css' => array(DirItemFilter::FILES, PsConst::EXT_CSS),
        'messages.msgs' => array(DirItemFilter::FILES, PsConst::EXT_MSGS),
        'info.rar' => array(DirItemFilter::FILES, DirItemFilter::ARCHIVES, PsConst::EXT_RAR),
        'info.zip' => array(DirItemFilter::FILES, DirItemFilter::ARCHIVES, PsConst::EXT_ZIP),
        '16x16.png' => array(DirItemFilter::FILES, DirItemFilter::IMAGES, PsConst::EXT_PNG),
        '32x32.png' => array(DirItemFilter::FILES, DirItemFilter::IMAGES, PsConst::EXT_PNG),
        '64x64.gif' => array(DirItemFilter::FILES, DirItemFilter::IMAGES, PsConst::EXT_GIF),
        '128x128.jpg' => array(DirItemFilter::FILES, DirItemFilter::IMAGES, PsConst::EXT_JPG),
        '256x256.jpeg' => array(DirItemFilter::FILES, DirItemFilter::IMAGES, PsConst::EXT_JPEG)
    );

    /**
     * Метод получения содержимого свалки.
     * Ко всем фильтрам будет добавлен DirItemFilter::ALL.
     */
    public static function SCRAP_CONTENT() {
        $SCRAP = array();
        foreach (self::$SCRAP as $name => $filters) {
            array_unshift($filters, DirItemFilter::ALL);
            $SCRAP[$name] = $filters;
        }
        return $SCRAP;
    }

    /**
     * Отфильтрованные названия элементов кучи
     */
    public static function SCRAP_FILTER($filter) {
        $filteredScrapItems = array();
        foreach (self::SCRAP_CONTENT() as $name => $filters) {
            if (!$filter || in_array($filter, $filters)) {
                $filteredScrapItems[] = $name;
            }
        }
        sort($filteredScrapItems);
        return $filteredScrapItems;
    }

    /**
     * Метод для очистки кучи перед выполнением тестов
     */
    public static function cleanScrap($absPath = null) {
        $dm = DirManager::inst($absPath ? $absPath : DirManagerTestHelper::scrapAbsPath());

        /* @var $di DirItem */
        foreach ($dm->getDirContent() as $name => $di) {
            if ($di->isDir()) {
                self::cleanScrap($di->getAbsPath());
            }
            if (array_key_exists($name, self::$SCRAP)) {
                continue; //---
            }
            $di->remove();
        }
    }

    /**
     * Все доступные фильтры
     */
    public static function allPossibleFilters() {
        $FILTER[] = null;
        $FILTER[] = DirItemFilter::getFilters();
        $FILTER[] = PsConst::getExts();

        return array_unique(to_array_expand($FILTER, true));
    }

}

?>