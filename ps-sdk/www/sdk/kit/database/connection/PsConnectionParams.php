<?php

/**
 * Класс для хранения параметров соединения с БД
 *
 * @author azazello
 */
final class PsConnectionParams {
    /**
     * Дефолтные названия соединений.
     * Порядок их в этом списке менять нельзя! Так как от этого порядка зависит 
     * порядок разворачивания схем, а первой должна разворачиваться тестовая схема.
     */

    const CONN_ROOT = 'root';
    const CONN_TEST = 'test';
    const CONN_PRODUCTION = 'production';

    /*
     * Названия параметров соединения
     */
    const PARAM_HOST = 'host';
    const PARAM_USER = 'user';
    const PARAM_PASSWORD = 'pwd';
    const PARAM_DATABASE = 'db';

    /**
     * Параметры коннекта в массиве
     */
    private $PARAMS = array();

    /**
     * URL - cтрока соединения в виде mysql://sdk:sdk@localhost/sdk_test
     */
    private $url;

    /**
     * URL - cтрока соединения в виде mysql://sdk:***@localhost/sdk_test
     * Для печати в tostring
     */
    private $toString;

    /**
     * В конструкторе проинициализируем все параметры соединения
     */
    private function __construct(array $params, $sourceDescr) {
        foreach (PsUtil::getClassConsts(__CLASS__, 'PARAM_') as $param) {
            $value = array_get_value(PsCheck::notEmptyString($param), $params);
            if (PsCheck::isNotEmptyString($value)) {
                $this->PARAMS[$param] = $value;
            } else {
                $this->PARAMS[$param] = null;
                //raise_error("Задано пустое значения для параметра $param в источнике $sourceDescr");
            }
        }

        $this->url = PsStrings::replaceWithBraced("{}://{}:{}@{}/{}", $this->scheme(), $this->user(), $this->password(), $this->host(), $this->database());
        $this->toString = PsStrings::replaceWithBraced("{}://{}:{}@{}/{} (source: {})", $this->scheme(), $this->user(), '***', $this->host(), $this->database(), $sourceDescr);
    }

    /**
     * Возвращает дефолтные названия соединений
     */
    public static function getDefaultConnectionNames() {
        return PsUtil::getClassConsts(__CLASS__, 'CONN_');
    }

    /**
     * Url строка в виде:
     * scheme://login:password@localhost/database
     * 
     * Пример:
     * mysql://ps-login:ps-password@localhost/ps
     */
    public function url() {
        return $this->url;
    }

    public function host() {
        return $this->PARAMS[self::PARAM_HOST];
    }

    public function user() {
        return $this->PARAMS[self::PARAM_USER];
    }

    public function password() {
        return $this->PARAMS[self::PARAM_PASSWORD];
    }

    public function database() {
        return $this->PARAMS[self::PARAM_DATABASE];
    }

    public function scheme() {
        return 'mysql';
    }

    /**
     * Проверка наличия настроек соединения
     */
    public static function has($connection, $scope = ENTITY_SCOPE_ALL) {
        return ConfigIni::hasProp(ConfigIni::GROUP_CONNECTIONS, $connection, $scope);
    }

    /**
     * Метод возвращает настройки подключения к БД
     * 
     * @param type $connection
     * @param type $scope
     * @return PsConnectionParams
     */
    public static function get($connection, $scope = ENTITY_SCOPE_ALL) {
        return new PsConnectionParams(ConfigIni::getPropCheckType(ConfigIni::GROUP_CONNECTIONS, $connection, array(PsConst::PHP_TYPE_ARRAY), $scope), "$connection/$scope");
    }

    /**
     * Метод возвращает настройки коннекта к боевой схеме
     * 
     * @return PsConnectionParams
     */
    public static function production() {
        return self::get(self::CONN_PRODUCTION);
    }

    /**
     * Метод возвращает настройки коннекта к тестовой схеме SDK
     * 
     * @return PsConnectionParams
     */
    public static function sdkTest() {
        return self::get(self::CONN_TEST, ENTITY_SCOPE_SDK);
    }

    public function __toString() {
        return $this->toString;
    }

    public function equals(PsConnectionParams $other = null) {
        return $other != null && ($this->url === $other->url);
    }

    /**
     * Выполняет команды в переданном файле в shell интерпретаторе
     */
    public function execureShell(DirItem $sql) {
        check_condition($sql->isFile(), 'Файл с sql инструкциями не существут: ' . $sql->getAbsPath());

        $LOGGER = PsLogger::inst(__CLASS__);
        $command = 'mysql'
                . ' --default-character-set=utf8'
                . ' --host=' . $this->host()
                . ' --user=' . $this->user()
                . ' --password=' . $this->password()
                . ($this->database() ? ' --database=' . $this->database() : '') //Если название схемы не указано (для рута) - выполняем быз указания схемы
                . ' < ' . $sql->getAbsPath();

        $LOGGER->info('Executing sql shell script: {}', $command);

        $output = shell_exec($command);

        $LOGGER->info($output ? 'Output: ' . $output : 'No uotput');
    }

}

?>