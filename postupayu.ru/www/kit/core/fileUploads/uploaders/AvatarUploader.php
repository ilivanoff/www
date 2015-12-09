<?php

/**
 * Загрузчик аватаров
 *
 * @author azazello
 */
class AvatarUploader extends FileUploader {

    protected function getAuthType() {
        return AuthManager::AUTH_TYPE_AUTHORIZED;
    }

    protected function getUploadType() {
        return UploadsBean::TYPE_AVATAR;
    }

    protected function isAutonomous() {
        return true;
    }

    protected function onBeforeSave(DirItem $source, $userId, ArrayAdapter $params) {
        $source->assertIsImg();
        check_condition($this->getUploadedFilesCount($userId) < 2, 'Больше нельзя загружать аватары');
    }

    protected function onAfterSave(DirItem $uploaded, $userId, ArrayAdapter $params) {
        
    }

}

?>