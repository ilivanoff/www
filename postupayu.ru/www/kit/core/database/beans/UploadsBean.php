<?php

/**
 * Бин для работы с загрузками
 *
 * @author Admin
 */
class UploadsBean extends BaseBean {

    const TYPE_AVATAR = 'A';

    private static function getTypes() {
        return PsUtil::getClassConsts(__CLASS__, 'TYPE_');
    }

    private static function assertValidType($type) {
        check_condition($type, 'Не передан тип загрузки файла');
        check_condition(in_array($type, self::getTypes()), "Тип загрузки [$type] не поддерживается");
        return $type;
    }

    /**
     * Вставляет запись о загруженном файле в базу
     */
    public function saveFileUpload($uploadType, $uploadName, $originalName, $mimeType, $userId, $params) {
        return $this->insert('
INSERT INTO ps_upload
(id_user, name, original_name, mime_type, type, v_params, dt_event, b_deleted) 
VALUES 
(?, ?, ?, ?, ?, ?, UNIX_TIMESTAMP(), 0)', array($userId, $uploadName, $originalName, $mimeType, self::assertValidType($uploadType), $params));
    }

    /**
     * Совсем удаляет запись о файле из базы (вызывается в случае ошибки)
     */
    public function clearFileUpload($uploadId) {
        if (is_numeric($uploadId)) {
            $this->update('delete from ps_upload where id_upload=?', $uploadId);
        }
    }

    /**
     * Построение запроса для обращения к БД
     */
    private function makeQuery($prefix, array &$params, $asc = null) {
        check_condition($prefix, 'Не передан запрос');

        $queryString = array(trim($prefix) . ' where type=? and b_deleted=0');
        $queryParams = array(self::assertValidType(array_get_value_unset('type', $params)));

        //id_upload
        $id_upload = array_get_value_unset('id_upload', $params);
        if (is_inumeric($id_upload)) {
            $queryString[] = 'id_upload=?';
            $queryParams[] = $id_upload;
        }

        //id_user
        $id_user = array_get_value_unset('id_user', $params);
        if (is_inumeric($id_user)) {
            $queryString[] = 'id_user is not null and id_user=?';
            $queryParams[] = $id_user;
        }

        check_condition(empty($params), 'Неизвестные ключи переданы для запроса файла: ' . array_to_string($params, false));

        $order = $asc === true ? ' order by dt_event asc, id_upload asc' : '';
        $order = $asc === false ? ' order by dt_event desc, id_upload desc' : '';

        $params = $queryParams;
        return implode(' and ', $queryString) . $order;
    }

    /**
     * Возвращает кол-во загруженных файлов
     */
    public function getFilesCount($uploadType, $userId = null) {
        $params['type'] = $uploadType;
        $params['id_user'] = $userId;
        return $this->getCnt($this->makeQuery('select count(1) as cnt from ps_upload', $params), $params);
    }

    /**
     * Возвращает коды загруженных файлов
     */
    public function getFilesIds($uploadType, $userId = null, $asc = true) {
        $params['type'] = $uploadType;
        $params['id_user'] = $userId;
        return $this->getIds($this->makeQuery('select id_upload as id from ps_upload', $params, $asc), $params);
    }

    /**
     * Проверяет наличие файла
     */
    public function hasFile($uploadType, $uploadId, $userId = null) {
        check_condition(is_numeric($uploadId), 'id_upload is not numeric');
        $params['type'] = $uploadType;
        $params['id_user'] = $userId;
        $params['id_upload'] = $uploadId;
        return $this->getCnt($this->makeQuery('select count(1) as cnt from ps_upload', $params), $params) > 0;
    }

    /**
     * Загружает файл
     */
    public function getFile($uploadType, $uploadId, $userId = null) {
        check_condition(is_numeric($uploadId), 'id_upload is not numeric');
        $params['type'] = $uploadType;
        $params['id_user'] = $userId;
        $params['id_upload'] = $uploadId;
        return $this->getRec($this->makeQuery('select * from ps_upload', $params), $params);
    }

    /**
     * Удаляет файл из базы
     */
    public function deleteFile($uploadType, $uploadId, $userId) {
        if ($this->hasFile($uploadType, $uploadId, $userId)) {
            $params['type'] = $uploadType;
            $params['id_user'] = $userId;
            $params['id_upload'] = $uploadId;
            return $this->update($this->makeQuery('update ps_upload set b_deleted=1', $params), $params) > 0;
        }
        return false;
    }

    /*
     * СИНГЛТОН
     */

    /** @return UploadsBean */
    public static function inst() {
        return parent::inst();
    }

    protected function __construct() {
        $consts = self::getTypes();
        check_condition(count($consts) == count(array_unique($consts)), 'Класс ' . __CLASS__ . ' содержит повторяющиеся константы: ' . array_to_string($consts, false));
        parent::__construct();
    }

}

?>