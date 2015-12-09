<?php

/**
 * Утилитный класс для работы с DbIni
 *
 * @author azazello
 */
class PsDbIniHelper {

    /**
     * Метод извлекает названия таблиц SDK из параметров вызова метода.
     * Мы должны принимать названия этих таблиц на вход, так как валидация
     * может проихсодить с опорой на текущие настройки db.ini, так и для случая
     * его перестройки на основе новой схемы, в которую могли быть добавлены/удалены
     * новые таблицы.
     * 
     * @param array $sdkTableNames
     * @return type
     */
    private static function extractSdkTableNames(array $sdkTableNames = null) {
        return check_condition($sdkTableNames === null ? DbIni::getSdkTables() : $sdkTableNames, 'Не переданы названия таблиц SDK');
    }

    /**
     * Метод валидирует все доступные db.ini
     */
    public static function validateAll() {
        PsProfiler::inst(__CLASS__)->start(__FUNCTION__);
        $errors = array();
        foreach (ConfigIni::getAllowedScopes() as $scope) {
            $err = self::validateIni($scope);
            if ($err) {
                $errors[$scope] = $err;
            }
        }
        PsProfiler::inst(__CLASS__)->stop();
        return $errors;
    }

    /**
     * Метод валидирует содержимое db.ini для заданного контекста.
     * Если db.ini не существует для проекта - это не ошибка.
     */
    public static function validateIni($scope) {
        return $scope == ENTITY_SCOPE_PROJ && !DbIni::existsIni($scope) ? array() : self::validateIniString($scope, DbIni::getIniContent($scope));
    }

    /**
     * Метод валидирует содержимое db.ini для указанного скоупа
     * и возвращает массив ошибок.
     */
    private static function validateIniString($scope, $iniContent, array $sdkTableNames = null) {
        $errors = array();
        try {
            self::validateIniStringImpl($scope, $iniContent, $errors, $sdkTableNames);
        } catch (PException $ex) {
            $errors[] = $ex->getMessage();
        }
        return $errors;
    }

    /**
     * Имплементация валидации
     */
    private static function validateIniStringImpl($scope, $iniContent, &$errors, array $sdkTableNames = null) {
        $sdkTableNames = self::extractSdkTableNames($sdkTableNames);

        check_condition($iniContent, "Empty db ini content for scope $scope");

        $iniTableProps = parse_ini_string($iniContent, true);
        $iniTables = array_keys($iniTableProps);

        /*
         * 1. Сначала проверим, что переданные таблицы относятся к своей области
         */
        switch ($scope) {
            case ENTITY_SCOPE_SDK:
                /*
                 * Мы сохраняем для SDK.
                 * Кол-во таблиц не должно измениться.
                 */
                $diff = array_values(array_diff($sdkTableNames, $iniTables));
                if ($diff) {
                    raise_error('Уменьшено количество таблиц SDK. Отсутствуют: ' . array_to_string($diff));
                }
                $diff = array_values(array_diff($iniTables, $sdkTableNames));
                if ($diff) {
                    raise_error('Увеличено количество таблиц SDK. Добавлены: ' . array_to_string($diff));
                }
                break;

            case ENTITY_SCOPE_PROJ:
                /*
                 * Мы сохраняем для проекта.
                 * Не должно быть настроек для SDK таблиц.
                 */
                $intersect = array_values(array_intersect($sdkTableNames, $iniTables));
                if ($intersect) {
                    raise_error('В описании проектных таблиц присутствуют таблицы SDK: ' . array_to_string($intersect));
                }
                break;
            default:
                raise_error("Unknown entity scope [$scope]");
        }

        /*
         * 2. Валидируем переданные свойства - проверяем, существуют ли таблицы и корректно ли заданы свойства
         */
        $dbTables = PsTable::all();

        foreach ($iniTableProps as $tableName => $tableProperties) {
            //Проверяем, существует ли таблица в БД
            if (array_key_exists($tableName, $dbTables)) {
                //Собираем ошибки конфигурации таблицы
                $errors = array_merge($errors, $dbTables[$tableName]->getConfigErrorsCustom($tableProperties));
            } else {
                $errors[] = "В БД отсутствует таблица '$tableName'";
            }
        }
    }

    /**
     * Метод валидирует содержимое db.ini для указанного скоупа и сохраняет db.ini.
     * Может использоваться для полной модификации db.ini, поэтому список SDK таблиц принимается на вход.
     */
    public static function validateAndSaveDbIniContent($scope, $iniContent, array $sdkTableNames = null) {
        $errors = self::validateIniString($scope, $iniContent, $sdkTableNames);
        if ($errors) {
            return $errors; //---
        }
        DbIni::saveIniContent($scope, $iniContent);
        return null; //----
    }

    /**
     * Метод валидирует настройки db.ini для указанного скоупа и сохраняет db.ini.
     * Может использоваться для полной модификации db.ini, поэтому список SDK таблиц принимается на вход.
     */
    public static function validateAndSaveDbIniTableProps($scope, array $tableSettings, array $sdkTableNames = null) {
        $sdkTableNames = self::extractSdkTableNames($sdkTableNames);
        $tableSettingsExtended = self::extendSdkDbIniSettings($scope, $tableSettings, $sdkTableNames);
        $iniLines = self::makeDbIniLinesOnSettings($tableSettingsExtended);
        $iniContent = implode("\n", $iniLines);

        $LOGGER = PsLogger::inst(__CLASS__);
        if ($LOGGER->isEnabled()) {
            $LOGGER->infoBox(__FUNCTION__);
            $LOGGER->info('<<SDK_TABLES>>');
            $LOGGER->info(print_r($sdkTableNames, true));
            $LOGGER->info('<<PROPERTIES>>');
            $LOGGER->info(print_r($tableSettings, true));
            $LOGGER->info();
            $LOGGER->info('<<PROPERTIES EXTENDED>>');
            $LOGGER->info(print_r($tableSettingsExtended, true));
            $LOGGER->info();
            $LOGGER->info('<<INI LINES>>');
            $LOGGER->info(print_r($iniLines, true));
            $LOGGER->info();
            $LOGGER->info('<<INI CONTENT>>');
            $LOGGER->info($iniContent);
        }

        return self::validateAndSaveDbIniContent($scope, $iniContent, $sdkTableNames);
    }

    /**
     * Метод расширяет настройки для SDK таблиц - недостающими.
     */
    private static function extendSdkDbIniSettings($scope, array $tableSettings, array $sdkTableNames) {
        if ($scope == ENTITY_SCOPE_SDK) {
            $notSelectedTables = array_diff($sdkTableNames, array_keys($tableSettings));
            foreach ($notSelectedTables as $tableName) {
                $tableSettings[$tableName] = array();
                /* @var $property PsTableColumnProps */
                foreach (PsTableColumnProps::getAllowedTableProperties(PsTable::inst($tableName)) as $propName => $property) {
                    $tableSettings[$tableName][$propName] = false;
                }
            }
        }
        return $tableSettings;
    }

    /**
     * Метод конвертирует настройки таблиц в строки db.ini.
     */
    private static function makeDbIniLinesOnSettings(array $tableSettings) {
        $lines = array();

        foreach ($tableSettings as $tableName => $tableOrColSetting) {
            if (count($lines)) {
                $lines[] = '';
            }
            $lines[] = "[$tableName]";
            foreach ($tableOrColSetting as $propName => $propValue) {
                if (is_array($propValue)) {
                    //Настройки для колонок
                    foreach ($propValue as $colName) {
                        $lines[] = $propName . '[]=' . trim($colName);
                    }
                } else {
                    //Настройка для таблицы
                    $lines[] = $propName . '=' . (isEmpty($propValue) ? '' : 'On');
                }
            }
        }

        return $lines;
    }

    /**
     * Метод формирует сщвуржимое db.ini файла для развёрнутой схемы.
     * 
     * За основу берутся текущие натсройки db.ini, которые могут быть расширены 
     * или сокращены (если таблица удалена из схемы).
     * 
     * @param string $scope - тип развёрнутой схемы.
     *                      Она может быть развёрнута, как SDK или как проектная.
     *                      Считаем, что в схему могли как добавиться новые таблицы,
     *                      так и кол-во таблиц в схеме могло быть сокращено.
     * @return array - настройки таблиц для db.ini
     */
    public static function makeDbIniForSchema($scope, array $dbTables) {
        /*
         * Список таблиц SDK можно получить из db.ini, так как если мы работаем по SDK,
         * то нужно взять старые настройки и порядок таблиц.
         * Если же мы работаем по проектному скоупу, то db.ini для SDK уже актуален.
         */
        $sdkTableNames = DbIni::getSdkTables();

        $settings = array();
        switch ($scope) {
            case ENTITY_SCOPE_SDK:
                foreach ($sdkTableNames as $tableName) {
                    if (!array_key_exists($tableName, $dbTables)) {
                        //Таблица была исключена из схемы
                        continue; //---
                    }

                    $table = array_get_value_unset($tableName, $dbTables);

                    $settings[$tableName] = array();

                    /* @var $property PsTableColumnProps */
                    foreach (PsTableColumnProps::getAllowedTableProperties($table) as $propName => $property) {
                        $settings[$tableName][$propName] = $table->isProperty($property);
                    }
                    /* @var $property PsTableColumnProps */
                    foreach (PsTableColumnProps::getAllowedColumnProperties($table) as $propName => $property) {
                        /* @var $col PsTableColumn */
                        foreach ($table->getColumns() as $colName => $col) {
                            if ($col->isProperty($property)) {
                                $settings[$tableName][$propName][] = $colName;
                            }
                        }
                    }
                }

                /*
                 * Добавляем несконфигурированные таблицы
                 */
                foreach ($dbTables as $tableName => $table) {
                    $settings[$tableName] = array();
                    /* @var $property PsTableColumnProps */
                    foreach (PsTableColumnProps::getAllowedTableProperties($dbTables[$tableName]) as $propName => $property) {
                        $settings[$tableName][$propName] = false;
                    }
                }

                break;

            case ENTITY_SCOPE_PROJ:
                //Сразу удаляем из списка все таблицы SDK
                array_remove_keys($dbTables, $sdkTableNames);

                foreach (DbIni::getProjectTables() as $tableName) {
                    if (!array_key_exists($tableName, $dbTables)) {
                        //Таблица была исключена из схемы или перенесена в SDK
                        continue; //---
                    }

                    $table = array_get_value_unset($tableName, $dbTables);

                    $settings[$tableName] = array();

                    /* @var $property PsTableColumnProps */
                    foreach (PsTableColumnProps::getAllowedTableProperties($table) as $propName => $property) {
                        $settings[$tableName][$propName] = $table->isProperty($property);
                    }
                    /* @var $property PsTableColumnProps */
                    foreach (PsTableColumnProps::getAllowedColumnProperties($table) as $propName => $property) {
                        /* @var $col PsTableColumn */
                        foreach ($table->getColumns() as $colName => $col) {
                            if ($col->isProperty($property)) {
                                $settings[$tableName][$propName][] = $colName;
                            }
                        }
                    }
                }

                /*
                 * Не добавляем несконфигурированные таблицы, так как для проектных 
                 * таблиц мы не требуем полного перечисления в db.ini
                 */

                break;

            default:
                raise_error("Unknown scope [$scope]");
        }

        return $settings;
    }

}

?>