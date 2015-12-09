<?php

class AdminPostsProcessor {
    /*
     * ВЫГРУЗКА ФОРМУЛ ИЗ ВСЕХ КОММЕНТАРИЕВ
     */

    private function getAllComments() {
        $formules = array();
        /* @var $proc PostsProcessor */
        foreach (Handlers::getInstance()->getPostsProcessors() as $proc) {
            $formules = array_merge($formules, $proc->getCommentsFormules());
        }
        return $formules;
    }

    public function saveCommentsFormules2Zip() {
        $formules = $this->getAllComments();

        if (empty($formules)) {
            return null; //---
        }

        $zipDi = DirManager::inst('admin/stuff', 'zip')->getDirItem(null, 'posts-comments', 'zip')->remove();
        $zip = $zipDi->startZip();

        $totalSize = 0;
        /** @var $imgDi DirItem */
        foreach ($formules as $imgDi) {
            $imgDi->setData('class', 'TeX');
            $totalSize += $imgDi->getSize();
            $name = $imgDi->getName();
            $path = 'formules/f' . $name[0] . '/f' . $name[1] . '/f' . $name[2] . '/';
            $zip->addFile($imgDi->getAbsPath(), $imgDi->getRelPath());
            $zip->addFile($imgDi->getAbsPath() . '.tex', $imgDi->getRelPath() . '.tex');
        }
        $zip->close();

        return array(
            'zip' => $zipDi,
            'formules' => $formules,
            'imagesSize' => $totalSize);
    }

    /*
     * 
     */

    /*
     * СИНГЛТОН
     */

    private static $inst;

    /** @return AdminPostsProcessor */
    public static function inst() {
        return self::$inst = (isset(self::$inst) ? self::$inst : new AdminPostsProcessor());
    }

    private function __construct() {
        
    }

}

?>
