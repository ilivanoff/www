<?php

/**
 * Базовый класс для изменения размеров картинок
 *
 * @author azazello
 */
final class PsImgEditor extends AbstractSingleton {

    /** @var SimpleDataCache */
    private $CAHCE;

    /** @return DirItem */
    public static function resizeBase($file, $dim) {
        return self::inst()->doResize(DirManager::images()->getDirItem('base', $file), $dim);
    }

    /** @return DirItem */
    public static function resize(DirItem $src, $dim, DirItem $dflt = null) {
        return self::inst()->doResize($src, $dim, $dflt);
    }

    public static function clean(DirItem $src = null) {
        if ($src instanceof DirItem) {
            self::inst()->doClean($src);
        }
    }

    public static function copy(DirItem $from, DirItem $to) {
        self::inst()->doCopy($from, $to);
    }

    /*
     * =================
     * = ИМПЛЕМЕНТАЦИЯ =
     * =================
     */

    private function doResize(DirItem $src, $dim, DirItem $dflt = null) {
        $dim = parse_dim($dim);
        $w = $dim[0];
        $h = $dim[1];
        $dim = $w . 'x' . $h;

        $resized = $this->doResizeImpl($src, $w, $h);

        if ($resized) {
            return $resized;
        }

        if (!($dflt instanceof DirItem)) {
            return null; //---
        }

        $dflt->assertIsImg();

        return $this->doResizeImpl($dflt, $w, $h);
    }

    private function doResizeImpl(DirItem $srcDi, $w, $h) {
        $dim = $w . 'x' . $h;
        $cacheKey = md5("[$srcDi]:[$dim]");

        if ($this->CAHCE->has($cacheKey)) {
            return $this->CAHCE->get($cacheKey);
        }

        $dstDi = DirManager::autogen("images/$dim")->cdToHashFolder(null, null, $cacheKey)->getDirItem(null, $cacheKey, SYSTEM_IMG_TYPE);
        if ($dstDi->isImg()) {
            return $this->CAHCE->set($cacheKey, $dstDi);
        }

        if (!$srcDi->isImg()) {
            return $this->CAHCE->set($cacheKey, null);
        }

        PsLock::lockMethod(__CLASS__, __FUNCTION__);
        try {
            if (!$dstDi->isImg()) {
                //Картинка не была пересоздана в другом потоке
                SimpleImage::inst()->load($srcDi)->resizeSmart($w, $h)->save($dstDi, SYSTEM_IMG_TYPE)->close();
            }
        } catch (Exception $ex) {
            PsLock::unlock();
            throw $ex;
        }
        PsLock::unlock();

        return $this->CAHCE->set($cacheKey, $dstDi);
    }

    private function doClean(DirItem $srcDi) {
        foreach (DirManager::autogen()->getSubDirNames('images') as $dim) {
            $cacheKey = md5("[$srcDi]:[$dim]");
            $this->CAHCE->remove($cacheKey);
            DirManager::autogen()->getHashedDirItem("images/$dim", $cacheKey, $cacheKey, SYSTEM_IMG_TYPE)->remove();
        }
    }

    private function doCopy(DirItem $from, DirItem $to) {
        $from->assertIsImg();

        PsLock::lockMethod(__CLASS__, __FUNCTION__);
        try {
            //Очистим нагенерённое для той картинки, В КОТОРУЮ копируем
            $this->doClean($to);
            //Картинка не была пересоздана в другом потоке
            SimpleImage::inst()->load($from)->save($to, SYSTEM_IMG_TYPE)->close();
        } catch (Exception $ex) {
            PsLock::unlock();
            throw $ex;
        }
        PsLock::unlock();
    }

    /** @return PsImgEditor */
    protected static function inst() {
        return parent::inst();
    }

    protected function __construct() {
        $this->CAHCE = new SimpleDataCache();
    }

}

?>