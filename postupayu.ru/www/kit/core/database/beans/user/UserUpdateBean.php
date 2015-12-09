<?php

/**
 * Класс хранит в себе функциональность по обновлению пользователя
 *
 * @author azazello
 */
abstract class UserUpdateBean extends UserLoadBean {

    /**
     * Поля, изменение которых не отражается в аудите
     */
    private static $SKIP_AUDIT_ON_UPDATE_FIELDS = array(
        self::FIELD_ABOUT, self::FIELD_CONTACTS, self::FIELD_MSG
    );

    /**
     * Поля, обновление которых запрещено
     */
    private static $UPDATE_DENY_FIELDS = array(
        self::FIELD_ID, self::FIELD_DT_REG,
        self::FIELD_B_ADMIN, self::FIELD_B_CAN_LOGIN
    );

    /**
     * Поля, обновляемые из формы редактирования параметров пользователя
     */
    private static $REG_EDIT_UPDATE_FIELDS = array(
        self::FIELD_NAME, self::FIELD_SEX,
        self::FIELD_ABOUT, self::FIELD_ABOUT_SRC,
        self::FIELD_CONTACTS, self::FIELD_CONTACTS_SRC,
        self::FIELD_MSG, self::FIELD_MSG_SRC
    );

    /**
     * Основной метод, выполняющий обновление пользователя
     * 
     * @param type $userId
     * @param array $whatAssoc
     * @param array $whereAssoc
     * @return type
     */
    private function updateUser($userId, array $whatAssoc, array $whereAssoc = array()) {
        //Сразу валидируем код пользователя
        $userId = AuthManager::validateUserId($userId);
        //В апдейте можно использовать только ассоциативные параметры
        Query::assertOnlyAssocParams($whatAssoc);
        //Получим список обновляемых колонок
        $columns = check_condition(array_keys($whatAssoc), 'Не переданы параметры обновления пользователя');
        //Проверим на наличие запрещённых полей
        $denyColumns = array_intersect(self::$UPDATE_DENY_FIELDS, $columns);
        if ($denyColumns) {
            raise_error('Cледующие параметры пользователя не могут быть обновлены: ' . array_to_string(array_values($denyColumns)));
        }
        //Проверим на наличие неизвестных полей
        $unknownColumns = array_diff($columns, self::getColumns());
        if ($unknownColumns) {
            raise_error('Попытка обновить недопустимые параметры пользователя: ' . array_to_string(array_values($unknownColumns)));
        }
        //Загружаем текущее состояние, на всякий случай предварительно сбросив кеш
        $OLD = $this->getUserDataById($this->reset($userId));
        //Сбрасываем кеш и выполняем обновление
        $whereAssoc[self::FIELD_ID] = $this->reset($userId);
        $updated = $this->update(Query::update('users', $whatAssoc, $whereAssoc));
        if ($updated <= 0) {
            return; //---
        }
        //Загружаем новое состояние
        $NEW = $this->getUserDataById($this->reset($userId));
        //Сравним и запишем аудит
        $DIF = array();
        foreach ($OLD as $column => $oldValue) {
            if (in_array($column, self::$SKIP_AUDIT_ON_UPDATE_FIELDS)) {
                continue; //---
            }
            $newValue = $NEW[$column];
            if (strcmp(trim($oldValue), trim($newValue)) != 0) {
                $DIF[$column] = $newValue;
            }
        }
        if (empty($DIF)) {
            return; //---
        }
        UserAudit::inst()->onUpdate($userId, $DIF);
    }

    /**
     * Не стоит забывать, что если новые значения совпадают со старыми, то при апдейте
     * мы не получим изменённых строк.
     */
    public function updateInfo($userId, RegFormData $regData) {
        $this->updateUser($userId, $regData->asAssocArray(self::$REG_EDIT_UPDATE_FIELDS));
    }

    /**
     * Обновление пароля
     */
    public function updatePass($userId, $plainPass) {
        $this->updateUser($userId, array(self::FIELD_PASSWD => self::hashPassword($plainPass)));
    }

    /**
     * Обновление временной зоны
     */
    public function updateTimezone($userId, $tzName) {
        $this->updateUser($userId, array(self::FIELD_TIMEZONE => $tzName));
    }

    /**
     * Установка аватара пользователя. Может быть передан и null, тогда аватар будет сброшен.
     */
    public function setUserAvatar($userId, $avatarId = null) {
        $this->updateUser($userId, array(self::FIELD_ID_AVATAR => $avatarId));
    }

    /**
     * Сброс текущего аватара пользователя. Во время обновления убедимся, что аватар принадлежал пользователю.
     */
    public function unsetUserAvatar($userId, $avatarId) {
        $this->updateUser($userId, array(self::FIELD_ID_AVATAR => null), array(self::FIELD_ID_AVATAR => $avatarId));
    }

}

?>