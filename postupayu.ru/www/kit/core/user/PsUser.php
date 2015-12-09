<?php

/**
 * Класс представляет собой пользователя системы и предоставляет все методы для работы с ним
 *
 * @author azazello
 */
class PsUser extends PsUserBase {

    /**
     * Смена пароля пользователя.
     * Пароль может быть обновлён по коду восстановления,
     * поэтому дадим возможность вызова без проверки прав.
     * @return PsUser
     */
    public function updatePassword($newPlainPass, $doAssert = true) {
        $this->assertCanEdit(__FUNCTION__, $doAssert);
        UserBean::inst()->updatePass($this->userId, $newPlainPass);
        return $this;
    }

    /**
     * Смена пароля пользователя
     */
    public function changePassword($oldPlainPass, $newPlainPass) {
        $this->assertCanEdit(__FUNCTION__);
        $this->checkPassword($oldPlainPass, true);
        $this->updatePassword($newPlainPass);
    }

    /**
     * Обновление временной зоны
     */
    public function updateTimezone($tzName) {
        $this->assertCanEdit(__FUNCTION__);
        UserBean::inst()->updateTimezone($this->userId, $tzName);
    }

    /**
     * Обновление информации по данному пользователю
     */
    public function updateInfo(RegFormData $regData) {
        $this->assertCanEdit(__FUNCTION__);
        UserBean::inst()->updateInfo($this->userId, $regData);
    }

    /**
     * Установка аватара для текущего пользователя
     * 
     * @param int $avatarId - может быть null, тогда аватар будет сброшен
     */
    public function setAvatar($avatarId) {
        $this->assertCanEdit(__FUNCTION__);
        return AvatarsBean::inst()->setUserAvatar($this->userId, $avatarId);
    }

    /**
     * Удаляет аватар пользователя
     */
    public function deteleAvatar($avatarId) {
        $this->assertCanEdit(__FUNCTION__);
        check_condition(is_inumeric($avatarId), 'Передан невалидный код аватара');
        PsImgEditor::clean(AvatarUploader::inst()->getUploadedFileDi($avatarId, $this->userId));
        return AvatarsBean::inst()->deleteUserAvatar($this->userId, $avatarId);
    }

    /*
     * 
     * ОБЩЕДОСТУПНЫЕ
     * 
     */

    const ID_CARD_AVATAR_DIM = '128x';

    /**
     * Получение файла с аватаром пользователя.
     * @return DirItem
     */
    public function getAvatarDi($dim = '100x100', $avtarId = null) {
        $avtarId = is_numeric($avtarId) ? $avtarId : $this->getAvatarId();
        $srcDi = $avtarId ? AvatarUploader::inst()->getUploadedFileDi($avtarId, $this->userId) : null;
        if ($srcDi && $srcDi->isImg()) {
            return PsImgEditor::resize($srcDi, $dim);
        }
        if (is_numeric($avtarId) || $this->hasAvatar()) {
            return PsImgEditor::resizeBase('noimage.png', $dim);
        }
        return $this->getDefaultAvatarDi($dim);
    }

    /**
     * Получение файла с дефолтным аватаром пользователя (соответствующим его полу).
     * @return DirItem
     */
    public function getDefaultAvatarDi($dim = '100x100') {
        return PsImgEditor::resizeBase($this->isBoy() ? 'male.jpg' : 'female.jpg', $dim);
    }

    /**
     * Метод возвращает относительный путь к аватарке пользователя
     */
    public function getAvatarRelPath($dim = '100x100', $avtarId = null) {
        return $this->getAvatarDi($dim, $avtarId)->getRelPath();
    }

    /**
     * Возвращает все аватары пользователя
     */
    public function getAvatarsList($includeDefault = false, $dim = '100x100') {
        $result = array();
        if ($includeDefault) {
            $result[PsConstJs::AVATAR_ID_PREFIX . PsConstJs::AVATAR_NO_SUFFIX] = $this->getDefaultAvatarDi($dim)->getRelPath();
        }
        foreach (AvatarUploader::inst()->getUploadedFilesIds($this->userId) as $avatarId) {
            $result[PsConstJs::AVATAR_ID_PREFIX . $avatarId] = $this->getAvatarRelPath($dim, $avatarId);
        }
        return $result;
    }

    /**
     * Возвращает <img src="..." /> с аватаром пользователя
     */
    public function getAvatarImg($dim = '100x100', array $params = array()) {
        return PsUserHelper::getAvatarImg($this, $dim, $params);
    }

    /**
     * Строит id-card с информацией о данном пользователе
     */
    public function getIdCard() {
        return PsHtml::div(array('class' => 'user_info'), $this->getIdCardContent());
    }

    /**
     * Строит содержимое для id-card
     */
    public function getIdCardContent() {
        return normalize_string(PSSmarty::template('idcard/content.tpl', array('user' => $this))->fetch());
    }

    /**
     * =============
     * = СИНГЛТОНЫ =
     * =============
     */
    private static $insts = array();

    /**
     * Возвращает экземпляр пользователя. Если не передан, то будет взят текущий (авторизованный).
     * 
     * Определяет и извлекает пользователя из переданных данных. Будем искать в:
     * 1. Массиве,если передан массив.
     * 2. Попытаемся привести число к нужному виду, если передано число.
     * 3. В сессии.
     * 
     * @return PsUser
     */
    public static function inst($DataOrId = null, $forceFill = false) {
        if (is_array($DataOrId)) {
            $userId = AuthManager::validateUserId(array_get_value('id_user', $DataOrId));
        } else {
            $userId = AuthManager::extractUserId($DataOrId);
        }

        if (!array_key_exists($userId, self::$insts)) {
            self::$insts[$userId] = null;
            self::$insts[$userId] = new PsUser($userId);
        }

        check_condition(self::$insts[$userId] instanceof PsUser, "Попытка повторно создать объект пользователя с кодом [$userId].");

        if ($forceFill) {
            //Проверим, что пользователь существует
            UserBean::inst()->getUserDataById($userId);
        }

        return self::$insts[$userId];
    }

    /**
     * Загрузка пользователя по email.
     * 
     * @param str $email - электронный адрес пользователя
     * @return PsUser
     */
    public static function instByMail($email) {
        $userId = UserBean::inst()->getUserIdByMail($email);
        check_condition(is_inumeric($userId), "Электронный адрес [$email] не зарегистрирован");
        return self::inst($userId);
    }

    /**
     * Возвращает экземпляр пользователя, если он авторизован, либо null.
     * 
     * @return PsUser
     */
    public static function instOrNull() {
        return AuthManager::isAuthorized() ? self::inst() : null;
    }

    /**
     * Дефолтный администратор
     * 
     * @return PsUser
     */
    public static function defaultAdmin() {
        return self::inst(DEFAULT_ADMIN_USER);
    }

    /**
     * Пользователь - система. Нужен для выполнения различных сервисных действий.
     * 
     * @return PsUser
     */
    public static function systemUser() {
        return self::inst(DEFAULT_SYSTEM_USER);
    }

}

?>