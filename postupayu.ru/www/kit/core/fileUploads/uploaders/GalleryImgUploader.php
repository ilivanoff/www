<?php

/**
 * Загрузчик картинок для галереи
 *
 * @author azazello
 */
class GalleryImgUploader extends FileUploader {

    const PARAM_GALLERY = 'gallery';

    protected function getAuthType() {
        return AuthManager::AUTH_TYPE_AUTHORIZED;
    }

    protected function getUploadType() {
        return null;
    }

    protected function isAutonomous() {
        return true;
    }

    protected function onBeforeSave(DirItem $source, $userId, ArrayAdapter $params) {
        $source->assertIsImg();
    }

    protected function onAfterSave(DirItem $uploaded, $userId, ArrayAdapter $params) {
        PsGallery::inst($params->str(self::PARAM_GALLERY))->addFileImg($uploaded);
    }

}

?>