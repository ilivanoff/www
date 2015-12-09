<?php

class GalleryBean extends BaseBean {

    /**
     * Получение информации о галерее. Пока - только название.
     */
    public function getGalleryInfo($gallery) {
        return $this->getRec('select v_name from ps_gallery where v_dir=?', $gallery);
    }

    /**
     * Получение списка картинок галлереи из БД
     * 
     * @param string $gallery - название галереи
     * @param bool $includeHidden - признак, включать ли картинки с b_show=0
     * @param array $checkFileSystemImgs - массив названий картинок файловой системы, чтобы отфильтровать несуществующие
     */
    public function getGalleryItems($gallery, $includeHidden = false, array $checkFileSystemImgs = null) {
        $bshow = $includeHidden ? '' : 'and b_show=1';
        $objects = $this->getObjects("select * from ps_gallery_images where v_dir=? $bshow order by n_order asc", $gallery, 'PsGalleryItem', 'v_file');

        $checkFiles = is_array($checkFileSystemImgs) ? array_diff(array_keys($objects), $checkFileSystemImgs) : array();
        foreach ($checkFiles as $file) {
            if (!$objects[$file]->isWeb()) {
                unset($objects[$file]);
            }
        }
        return $objects;
    }

    private function saveImg($gallery, array $img, $order) {
        check_condition($gallery, 'Не задано название галереи');
        $file = check_condition(array_get_value('file', $img), 'Не задан путь к картинке');
        $this->update('
INSERT INTO ps_gallery_images 
(v_dir, v_file, b_show, b_web, v_name, v_descr, n_order) 
VALUES 
(?, ?, ?, ?, ?, ?, ?)', array(
            $gallery, //
            $file, //
            !isEmptyInArray('show', $img), //
            !isEmptyInArray('web', $img), //
            $img['name'], //
            $img['descr'], //
            $order)
        );
    }

    /**
     * Сохранение картинок галлерей в БД
     */
    public function saveGallery($gallery, $name, array $images) {
        AuthManager::checkAdminAccess();

        $cnt = $this->getCnt('select count(1) as cnt from ps_gallery where v_dir=?', $gallery);
        if ($cnt == 0) {
            $this->insert('insert into ps_gallery (v_dir, v_name) VALUES (?, ?)', array($gallery, $name));
        } else {
            $this->update('update ps_gallery set v_name=? where v_dir=?', array($name, $gallery));
        }

        $this->update('delete from ps_gallery_images where v_dir=?', $gallery);

        $order = 0;
        foreach ($images as $img) {
            $this->saveImg($gallery, $img, ++$order);
        }
    }

    /**
     * Удаляем WEB картинку из галереи
     */
    public function deleteWebImg($gallery, $file) {
        $this->update('delete from ps_gallery_images where v_dir=? and v_file=? and b_web=1', array($gallery, $file));
    }

    /**
     * Удаляем обычную картинку из галереи
     */
    public function deleteLocalImg($gallery, $file) {
        $this->update('delete from ps_gallery_images where v_dir=? and v_file=? and b_web=0', array($gallery, $file));
    }

    /**
     * Удаляем WEB картинку из галереи
     */
    public function addWebImg($gallery, array $img) {
        $order = $this->getCnt('select IFNULL(max(n_order), 0) + 1 as cnt from ps_gallery_images where v_dir=?', $gallery);
        $img['web'] = 1;
        $img['show'] = 0;
        $this->saveImg($gallery, $img, $order);
    }

    /*
     * СИНГЛТОН
     */

    /** @return GalleryBean */
    public static function inst() {
        return parent::inst();
    }

}

?>