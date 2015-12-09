<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AbstractConfigIni
 *
 * @author azazello
 */
abstract class AbstractIni {

    /**
     * Обработанные настройки.
     * config.ini => scope => settings_array
     */
    private static $INI = array();

    /**
     * Привязка названия класса к названию файла.
     * ConfigIni => config.ini
     */
    private static $CLASS2FILE = array();

    /**
     * Название класса
     */
    public static final function getClass() {
        return get_called_class();
    }

    /**
     * Название файла конфига
     */
    public static function getConfigName() {
        $class = self::getClass();
        if (array_key_exists($class, self::$CLASS2FILE)) {
            return self::$CLASS2FILE[$class];
        }
        if ($class == __CLASS__) {
            PsUtil::raise('Illegal to call {}::{}', __CLASS__, __FUNCTION__);
        }
        return self::$CLASS2FILE[$class] = cut_string_end(strtolower($class), 'ini') . '.ini';
    }

    /**
     * Получает ссылку на файл с конфигом .ini
     */
    private static function getIniDi($scope) {
        switch ($scope) {
            case ENTITY_SCOPE_SDK:
                return DirManagerSdk::sdk()->getDirItem(DirManagerSdk::DIR_CONFIG, self::getConfigName());
            case ENTITY_SCOPE_PROJ:
                return DirManagerSdk::inst()->getDirItem(DirManagerSdk::DIR_CONFIG, self::getConfigName());
        }
        PsUtil::raise('Invalid scope [{}] for method {}::{}', $scope, __CLASS__, __FUNCTION__);
    }

    /**
     * Проверка существования самого ini афйла
     */
    public static function existsIni($scope) {
        return self::getIniDi($scope)->isFile();
    }

    /**
     * Проверка существования проектного ini
     */
    public static function existsProj() {
        return self::existsIni(ENTITY_SCOPE_PROJ);
    }

    /**
     * Проверка существования ini файла в sdk
     */
    public static function existsSdk() {
        return self::existsIni(ENTITY_SCOPE_SDK);
    }

    /**
     * Получение содержимого ini афйла
     */
    public static function getIniContent($scope) {
        return self::getIniDi($scope)->getFileContents(false);
    }

    /**
     * Получение содержимого ini файла
     */
    public static function saveIniContent($scope, $content) {
        self::getIniDi($scope)->putToFile($content);
        unset(self::$INI[self::getConfigName()]);
    }

    /**
     * Метод загружает все группы настроек
     */
    public static function getIni($scope = ENTITY_SCOPE_ALL) {
        $config = self::getConfigName();

        if (!array_key_exists($config, self::$INI)) {

            $LOGGER = PsLogger::inst(__CLASS__);

            $sdkDi = self::getIniDi(ENTITY_SCOPE_SDK);
            $projDi = self::getIniDi(ENTITY_SCOPE_PROJ);

            self::$INI[$config] = array();
            self::$INI[$config][ENTITY_SCOPE_SDK] = $sdkDi->parseAsIni(true);
            self::$INI[$config][ENTITY_SCOPE_PROJ] = to_array($projDi->parseAsIni(true, false));
            self::$INI[$config][ENTITY_SCOPE_ALL] = PsUtil::mergeIniFiles(self::$INI[$config][ENTITY_SCOPE_SDK], self::$INI[$config][ENTITY_SCOPE_PROJ]);

            if ($LOGGER->isEnabled()) {
                foreach (self::$INI[$config] as $iniScope => $iniProps) {
                    $LOGGER->info('{} [{}]:', $config, $iniScope);
                    $LOGGER->info(print_r($iniProps, true));
                    $LOGGER->info();
                }
            }
        }

        check_condition(array_key_exists($scope, self::$INI[$config]), "Unknown entity scope: $scope");

        return self::$INI[$config][$scope];
    }

    /**
     * Проверка существования группы
     */
    public static function hasGroup($group, $scope = ENTITY_SCOPE_ALL) {
        return array_key_exists($group, self::getIni($scope));
    }

    /**
     * Возвращает все группы заданного scope
     */
    public static function getGroups($scope = ENTITY_SCOPE_ALL) {
        return array_keys(self::getIni($scope));
    }

    /**
     * Загрузка настроек конкретной группы
     */
    public static function getGroup($group, $mandatory = true, $scope = ENTITY_SCOPE_ALL) {
        if (self::hasGroup($group, $scope)) {
            return self::getIni($scope)[$group];
        }
        if ($mandatory) {
            PsUtil::raise('Required config group [{}] not found in {} [{}]', $group, static::getConfigName(), $scope);
        }
        return null; //--
    }

    /**
     * Загрузка настроек конкретной группы или null
     */
    public static function getGroupOrNull($group, $scope = ENTITY_SCOPE_ALL) {
        return self::getGroup($group, false, $scope);
    }

    /**
     * Проверка существования свойства
     */
    public static function hasProp($group, $prop, $scope = ENTITY_SCOPE_ALL) {
        return self::hasGroup($group, $scope) && array_key_exists($prop, self::getGroup($group, true, $scope));
    }

    /**
     * Загрузка конкретной настройки
     */
    public static function getProp($group, $prop, $mandatory = true, $scope = ENTITY_SCOPE_ALL) {
        if (self::hasProp($group, $prop, $scope)) {
            return self::getGroup($group, true, $scope)[$prop];
        }
        if ($mandatory) {
            PsUtil::raise('Required config property [{}/{}] not found in {} [{}]', $group, $prop, static::getConfigName(), $scope);
        }
        return null; //--
    }

    /**
     * Загрузка конкретной настройки или null
     */
    public static function getPropOrNull($group, $prop, $scope = ENTITY_SCOPE_ALL) {
        return self::getProp($group, $prop, false, $scope);
    }

    /**
     * Загрузка конкретной настройки с проверкой её типа
     */
    public static function getPropCheckType($group, $prop, array $allowedTypes = null, $scope = ENTITY_SCOPE_ALL) {
        return PsCheck::phpVarType(self::getPropOrNull($group, $prop, $scope), $allowedTypes);
    }

}

?>