<?php

/**
 * Класс - оболочка элемента галлереи.
 * 
 * Может быть создан двумя способами:
 * 1. Получен из базы (для тех картинок, информация о которых была сохранена в БД)
 * 2. Построен на основе DirItem картинки галереи (для тех картинок, информация о которых ещё не была сохранеа в БД)
 * 
 * Заведём константы, чтобы убедиться в том, что все свойства установлены.
 */
class PsGalleryItem extends BaseDataStore {

    const DBPROP_NAME = 'v_name';
    const DBPROP_DESCR = 'v_descr';
    const DBPROP_DIR = 'v_dir';
    const DBPROP_FILE = 'v_file';
    const DBPROP_SHOW = 'b_show';
    const DBPROP_WEB = 'b_web';

    /**
     * Возвращает класс-оболочку для элемента галереи.
     * 
     * @return PsGalleryItem
     */
    public static function inst($ArrOrDi) {
        if ($ArrOrDi instanceof DirItem) {
            $data = array();
            $data[self::DBPROP_NAME] = '';
            $data[self::DBPROP_DESCR] = '';
            $data[self::DBPROP_DIR] = basename($ArrOrDi->getDirname());
            $data[self::DBPROP_FILE] = $ArrOrDi->getName();
            $data[self::DBPROP_SHOW] = false;
            $data[self::DBPROP_WEB] = false;
            $ArrOrDi = $data;
        }
        return new PsGalleryItem($ArrOrDi);
    }

    /**
     * "Читабельное" название картинки
     */
    public function getName() {
        return parent::__get(self::DBPROP_NAME);
    }

    /**
     * "Читабельное" примечание к картинке
     */
    public function getDescr() {
        return parent::__get(self::DBPROP_DESCR);
    }

    /**
     * Название папки с картинками
     */
    public function getDir() {
        return parent::__get(self::DBPROP_DIR);
    }

    /**
     * Название файла в папке с картинками
     */
    public function getFile() {
        return parent::__get(self::DBPROP_FILE);
    }

    /**
     * Признак - показываем ли данную картинку в галерее
     */
    public function isShow() {
        return !!parent::__get(self::DBPROP_SHOW);
    }

    /**
     * Признак - является ли картинка внешней
     */
    public function isWeb() {
        return !!parent::__get(self::DBPROP_WEB);
    }

    /**
     * Относительный путь к файлу
     */
    public function getRelPath() {
        return $this->isWeb() ? $this->getFile() : DirManager::gallery()->relFilePath($this->getDir(), $this->getFile());
    }

    /**
     * Переопределим конструктоп для проверки наличия всех нужных нам свойств.
     * Данная проверка нужна, чтобы не забыть определить все свойство, так как у нас два источника элементов галереи - база и файловая система.
     */
    function __construct(array $data) {
        foreach (PsUtil::getClassConsts(__CLASS__, 'DBPROP_') as $propName) {
            check_condition(array_key_exists($propName, $data), "Required property $propName not given for " . __CLASS__);
        }
        parent::__construct($data);
    }

}

?>
