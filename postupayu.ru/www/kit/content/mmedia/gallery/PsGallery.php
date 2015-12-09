<?php

/**
 * TODO
 * 1. Вынести свойства на константы
 * 2. Дорефакторить и переименовать общие методы
 * 3. Использовать PsConstJs для констант
 */
class PsGallery {

    const PARAM_CNT = 'cnt';
    const PARAM_NAME = 'name';
    const PARAM_ASLIST = 'list';
    const PARAM_BOXIMGS = 'boxImages';

    /** @var DirManager */
    private $DM;

    /** Название директории, в которой лежат картинки галереи */
    private $gallery;

    private function __construct($gallery) {
        $this->gallery = $gallery;
        $this->DM = DirManager::gallery($gallery);
        check_condition($this->DM->isDir(), "Запрошена несуществующая галерея [$gallery]");
    }

    private static $insts = array();
    private static $allLoaded = false;

    /**
     * Получение экземпляра галереи
     * 
     * @return PsGallery
     */
    public static function inst($gallery) {
        if (!array_key_exists($gallery, self::$insts)) {
            self::$insts[$gallery] = new PsGallery($gallery);
        }
        return self::$insts[$gallery];
    }

    /**
     * Получение экземпляров всех ралерей
     */
    public static function allInsts() {
        if (!self::$allLoaded) {
            self::$allLoaded = true;
            foreach (DirManager::gallery()->getSubDirNames() as $gallery) {
                self::inst($gallery);
            }
            ksort(self::$insts);
        }
        return self::$insts;
    }

    /**
     * Создание новой галереи. Будет создана директория и запись в базе.
     */
    public static function makeNew($gallery, $name) {
        AuthManager::checkAdminAccess();
        check_condition($gallery, 'Не передано название галереи');
        check_condition(!array_key_exists($gallery, self::allInsts()), "Галерея [$gallery] уже существует");
        DirManager::gallery()->makePath($gallery);
        self::inst($gallery)->saveGallery($name, array());
    }

    /**
     * Метод получает названия картинок, хранящихся в директории
     */
    private function getDirectoryImgNames() {
        return $this->DM->getDirContent(null, DirItemFilter::IMAGES, DirManager::DC_NAMES);
    }

    /**
     * Метод получает карту картинок, хранящихся в директории (название=>DirItem)
     */
    private function getDirectoryImgDirItems() {
        return $this->DM->getDirContent(null, DirItemFilter::IMAGES);
    }

    /**
     * Получение полной информации о галерее
     */

    /**
     * Метод собирает всю необходимую информацию о галерее и кеширует её для быстрого доступа.
     */
    private function getSnapshot() {
        $DATA = PSCache::GALLERY()->getFromCache($this->gallery, PsUtil::getClassConsts($this, 'PARAM_'));

        if (is_array($DATA)) {
            return $DATA; //---
        }

        $DATA = array();

        /*
         * Информация о галерее (из базы)
         */
        $galleryInfo = GalleryBean::inst()->getGalleryInfo($this->gallery);

        //name
        $gallName = $galleryInfo ? trim($galleryInfo['v_name']) : '';
        $gallName = $gallName ? $gallName : 'Галерея';

        /*
         * Картинки, входящие в галерею (те, для которых есть запись в БД и b_show=1).
         * Мы также сразу отсеим те картинки, для которых нет файла на файловой системе,
         * чтобы потом клиент не делал лишний запрос на наш сервер.
         */
        $galleryImages = GalleryBean::inst()->getGalleryItems($this->gallery, false, $this->getDirectoryImgNames());

        //Проведём фетчинг необходимых представлений галереи
        $SmartyParams['id'] = $this->gallery;
        $SmartyParams['name'] = $gallName;
        $SmartyParams['images'] = $galleryImages;

        //.box_images - блок картинок, который будет преобразован в галерею с помощью js.
        $imagesHtml = PSSmarty::template('mmedia/gallery/box_images.tpl', $SmartyParams)->fetch();
        $imagesHtml = normalize_string($imagesHtml);

        //.as_list - для отображения списка картинок в popup окне
        $asListHtml = PSSmarty::template('mmedia/gallery/as_list.tpl', $SmartyParams)->fetch();
        $asListHtml = normalize_string($asListHtml);

        //Все сложим в файл и сохраним в кеш
        $DATA[self::PARAM_CNT] = count($galleryImages);
        $DATA[self::PARAM_NAME] = $gallName;
        $DATA[self::PARAM_ASLIST] = $asListHtml;
        $DATA[self::PARAM_BOXIMGS] = $imagesHtml;

        return PSCache::GALLERY()->saveToCache($DATA, $this->gallery);
    }

    private function getSnapshotInfo($PARAM) {
        return array_get_value($PARAM, $this->getSnapshot());
    }

    /**
     * Параметры галереи
     */
    //Блок картинок, который будет преобразован в галерею с помощью js
    public function getCount() {
        return $this->getSnapshotInfo(self::PARAM_CNT);
    }

    public function getBoxImages() {
        return $this->getSnapshotInfo(self::PARAM_BOXIMGS);
    }

    //.as_list - для отображения в popup окне в виде списка
    public function getListImages() {
        return $this->getSnapshotInfo(self::PARAM_ASLIST);
    }

    public function getName() {
        return $this->getSnapshotInfo(self::PARAM_NAME);
    }

    public function getDir() {
        return $this->gallery;
    }

    /**
     * {gallery dir='trainings' lazy=1}
     */
    public function getGalleryBox($isLazy) {
        $DATA = $this->getSnapshot();

        $DATA['id'] = $this->gallery;
        $DATA['lazy'] = $isLazy;

        return PSSmarty::template('mmedia/gallery/box.tpl', $DATA)->fetch();
    }

    /**
     * Возвращает все картинки галереи для отображения в админке.
     */
    public function getAllGalleryItems() {
        AuthManager::checkAdminAccess();

        $galleryImages = GalleryBean::inst()->getGalleryItems($this->gallery, true);

        /* @var $dirItem DirItem */
        foreach ($this->getDirectoryImgDirItems() as $file => $dirItem) {
            if (!array_key_exists($file, $galleryImages)) {
                $galleryImages[$file] = PsGalleryItem::inst($dirItem);
            }
        }
        return $galleryImages;
    }

    /**
     * Сохранение галереи
     */
    public function saveGallery($name, array $images) {
        AuthManager::checkAdminAccess();
        GalleryBean::inst()->saveGallery($this->gallery, $name, $images);
    }

    public function addWebImg(array $img) {
        AuthManager::checkAdminAccess();
        GalleryBean::inst()->addWebImg($this->gallery, $img);
    }

    public function addFileImg(DirItem $img) {
        AuthManager::checkAdminAccess();
        //todo - НЕБЕЗОПАСНО! Разобраться с преобразованием картинок. Они портятся при перегонке формата
        $img->copyTo($this->DM->absFilePath(null, $img->getNameNoExt() . '_' . getRandomString(3), array_get_value(1, explode('/', $img->getMime()))));
        //SimpleImage::inst()->load($img)->save($this->DM->getDirItem(null, $img->getNameNoExt()), 'png')->close();
    }

    public function deleteWebImg($file) {
        AuthManager::checkAdminAccess();
        GalleryBean::inst()->deleteWebImg($this->gallery, $file);
    }

    public function deleteLocalImg($file) {
        AuthManager::checkAdminAccess();
        GalleryBean::inst()->deleteLocalImg($this->gallery, $file);
        $this->DM->getDirItem(null, $file)->remove();
    }

}

?>