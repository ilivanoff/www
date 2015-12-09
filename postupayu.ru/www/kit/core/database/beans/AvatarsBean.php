<?php

/**
 * Description of AvatarsBean
 *
 * @author Admin
 */
class AvatarsBean extends BaseBean {

    /**
     * Назначает аватар пользователю
     * 
     * @param int $userId - код пользователя
     * @param int $avatarId - код аватара (может быть null, если мы сбрасываем аватар)
     */
    public function setUserAvatar($userId, $avatarId = null) {
        //Валидируем входные параметры
        $avatarId = PsCheck::intOrNull($avatarId);
        $userId = AuthManager::validateUserId($userId);
        if (is_integer($avatarId) && !AvatarUploader::inst()->hasUploadedFile($avatarId, $userId)) {
            return false;
        }
        UserBean::inst()->setUserAvatar($userId, $avatarId);
        return true;
    }

    /**
     * Возвращает коды аватаров пользователя
     */
    public function getUserAvatars($userId) {
        return AvatarUploader::inst()->getUploadedFilesIds($userId);
    }

    /**
     * Метод удаляет аватар (с удалением загруженной картинки)
     */
    public function deleteUserAvatar($userId, $avatarId) {
        $avatarId = PsCheck::int($avatarId);
        UserBean::inst()->unsetUserAvatar($userId, $avatarId);
        return AvatarUploader::inst()->deleteUploadedFile($avatarId, $userId);
    }

    /*
     * СИНГЛТОН
     */

    /** @return AvatarsBean */
    public static function inst() {
        return parent::inst();
    }

}

?>
