<?php

/**
 * Класс для быстрой загрузки файлов в систему - он не работает с базой
 * и не стирает файл после его загрузки.
 *
 * @author azazello
 */
class SimpleUploader extends FileUploader {

    /**
     * О проверке безопасности должен позаботиться вызывающий код
     */
    protected function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    /**
     * С базой данный класс не работает.
     * Если это нужно - пишите полноценный FileUploader.
     */
    protected function getUploadType() {
        return null;
    }

    /**
     * Не будем удалять файлы после обработки, это - задача внешнего кода.
     */
    protected function isAutonomous() {
        return false;
    }

    /**
     * Никаких действий, всё будет выполнено в вызывающем коде
     */
    protected function onBeforeSave(DirItem $source, $userId, ArrayAdapter $params) {
        
    }

    /**
     * Никаких действий, всё будет выполнено в вызывающем коде
     */
    protected function onAfterSave(DirItem $uploaded, $userId, ArrayAdapter $params) {
        
    }

}

?>
