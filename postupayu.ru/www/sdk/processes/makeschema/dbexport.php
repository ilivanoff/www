<?php

function executeProcess(array $argv) {
    PsConnectionPool::assertDisconnectied();

    /*
     * СОЗДАЁМ SQL
     * 
     * Нам нужны настройки таблиц, которые неоткуда взять, кроме как из базы, поэтому для экспорта данных нужна БД.
     * Все данные из "редактируемых" таблиц также загружаются из этой БД.
     */
    $DB = DirManager::inst(__DIR__ . '/temp');
    $SDK = DirManager::inst(__DIR__ . '/temp/sdk');

    //Почистим файлы для удаления
    dolog('Clearing not .sql/.txt from temp dir');
    /* @var $item DirItem */
    foreach ($DB->getDirContentFull(null, DirItemFilter::FILES) as $item) {
        if (!$item->checkExtension(array(PsConst::EXT_TXT, PsConst::EXT_SQL))) {
            dolog('[-] {}', $item->remove()->getRelPath());
        }
    }
    dolog();

    /*
     * SDK
     */
    //dolog('Processing objects.sql');

    /* @var $DIR DirManager */
    foreach (array(ENTITY_SCOPE_SDK => $SDK, ENTITY_SCOPE_PROJ => $DB) as $scope => $DM) {
        dolog();
        dolog('***************************************************************');
        dolog('>>> Processing scope [{}], dir: [{}]', $scope, $DM->absDirPath());

        $SCHEMA = $DM->getDirItem(null, 'schema.sql');
        if (!$SCHEMA->isFile()) {
            dolog('schema.sql is not exists, skipping');
            continue; //---
        }

        $DM_SYSOBJECTS = DirManager::inst($DM->absDirPath(), 'sysobjects');

        /*
         * Очищаем папку, в которой будет содержимое для автосгенерированных файлов
         */
        $DM_AUTO = DirManager::inst($DM_SYSOBJECTS->absDirPath(), 'auto')->clearDir();

        /*
         * Создадим ссылку на файл с данными, выгруженными из таблиц
         */
        $DI_AUTO_DATA = $DM_AUTO->getDirItem(null, 'data.sql');

        /*
         * Сначала сделаем триггеры на таблицы, от которых зависит кеш и к которым привязаны фолдинги.
         */
        LOGBOX_INIT();
        LOGBOX('Making cache and folding table triggers');

        $DM_AUTO_TRIGGERS = $DM_AUTO->getDirItem('triggers')->makePath();
        $DI_AUTO_TRIGGERS_SQL = $DM_AUTO->getDirItem(null, 'triggers', 'sql')->getSqlFileBuilder();

        $triggerPattern = $SDK->getDirItem('sysobjects/patterns', 'ta_iud_pattern.sql')->getFileContents();
        //BUILDIAN AND ADDING TRIGGERS
        foreach (PsTriggersAware::getTriggeredTables($scope) as $table) {
            foreach (PsTriggersAware::getActions() as $action) {
                $actions = PsTriggersAware::getTriggerActions($table, $scope, $action);
                if (empty($actions)) {
                    continue; //---
                }
                $name = 'ta' . strtolower(first_char($action)) . '_' . $table;
                $trigger = str_replace('<%NAME%>', $name, $triggerPattern);
                $trigger = str_replace('<%TABLE%>', $table, $trigger);
                $trigger = str_replace('<%ACTION%>', $action, $trigger);
                $trigger = str_replace('<%CALL%>', implode("\n", $actions), $trigger);

                dolog("+ $name");
                foreach ($actions as $_action) {
                    dolog($_action);
                }

                $DI_AUTO_TRIGGERS_SQL->appendFile($DM_AUTO_TRIGGERS->getDirItem(null, $name, 'sql')->putToFile($trigger));
            }
        }

        //Все автоматически сгенерированные триггеры положим в отдельный файл
        $DI_AUTO_TRIGGERS_SQL->save();

        $autoTriggers = DirManager::inst($DM_AUTO_TRIGGERS->getAbsPath())->getDirContent(null, PsConst::EXT_SQL);
        dolog('Triggers made: {}', count($autoTriggers));

        /*
         * objects.sql
         */
        $OBJECTS_SQL = $DM_AUTO->getDirItem(null, 'objects.sql')->getSqlFileBuilder();

        /*
         * Добавляем автосгенерированные триггеры
         */
        LOGBOX('Processing objects.sql');

        if (empty($autoTriggers)) {
            dolog('No auto triggers');
        } else {
            dolog('Adding {} auto triggers', count($autoTriggers));

            $OBJECTS_SQL->appendMlComment('AUTO TRIGGERS SECTION');
            /* @var $triggerDi DirItem */
            foreach ($autoTriggers as $triggerDi) {
                dolog('+ {}', $triggerDi->getName());
                $OBJECTS_SQL->appendFile($triggerDi);
            }
        }

        /*
         * Получаем строки с включениями
         */
        $ALL = $DM_SYSOBJECTS->getDirItem(null, 'all.txt')->getFileLines(false);
        if (empty($ALL)) {
            dolog('No includes');
        } else {
            dolog('Adding {} includes from all.txt', count($ALL));

            $OBJECTS_SQL->appendMlComment('INCLUDES SECTION');
            foreach ($ALL as $include) {
                dolog('+ {}', $include);
                $OBJECTS_SQL->appendFile($DM_SYSOBJECTS->getDirItem($include));
            }
        }


        $OBJECTS_SQL->save();

        /*
         * Создаём скрипты инициализации для схем
         */
        foreach (PsConnectionParams::getDefaultConnectionNames() as $connection) {
            if (PsConnectionParams::has($connection, $scope)) {
                $props = PsConnectionParams::get($connection, $scope);
                $database = $props->database();

                if (empty($database)) {
                    continue; //Не задана БД - пропускаем (для root)
                }

                LOGBOX('Making schema script for {}', $props);

                $SCHEMA_SQL = $DM_AUTO->getDirItem('schemas', $database, 'sql')->makePath()->getSqlFileBuilder();

                //DROP+USE
                $SCHEMA_SQL->clean();
                $SCHEMA_SQL->appendLine("DROP DATABASE IF EXISTS $database;");
                $SCHEMA_SQL->appendLine("CREATE DATABASE $database CHARACTER SET utf8 COLLATE utf8_general_ci;");
                $SCHEMA_SQL->appendLine("USE $database;");

                if ($scope == ENTITY_SCOPE_PROJ) {
                    dolog('+ SDK PART');

                    //Добавим секцию в лог
                    $SCHEMA_SQL->appendMlComment('>>> SDK');

                    //CREATE CHEMA SCRIPT
                    $SCHEMA_SQL->appendFile($SDK->getDirItem(null, 'schema.sql'));

                    //OBJECTS SCRIPT
                    $SCHEMA_SQL->appendFile($SDK->getDirItem('sysobjects/auto', 'objects.sql'));

                    //Добавим секцию в лог
                    $SCHEMA_SQL->appendMlComment('<<< SDK');
                }

                //CREATE CHEMA SCRIPT
                $SCHEMA_SQL->appendFile($SCHEMA);

                //OBJECTS SCRIPT
                $SCHEMA_SQL->appendFile($OBJECTS_SQL->getDi());

                //CREATE USER
                $grant = "grant all on {}.* to '{}'@'{}' identified by '{}';";
                $SCHEMA_SQL->appendMlComment('Grants');
                $SCHEMA_SQL->appendLine(PsStrings::replaceWithBraced($grant, $database, $props->user(), $props->host(), $props->password()));

                /*
                 * Мы должны создать тестовую схему, чтобы убедиться, что всё хорошо и сконфигурировать db.ini
                 */
                if ($connection == PsConnectionParams::CONN_TEST) {
                    dolog('Making physical schema {}', $props);

                    $rootProps = PsConnectionParams::get(PsConnectionParams::CONN_ROOT);
                    dolog('Root connection props: {}', $rootProps);

                    $rootProps->execureShell($SCHEMA_SQL->getDi());

                    dolog('Connecting to [{}]', $props);
                    PsConnectionPool::configure($props);

                    $tables = PsTable::all();

                    /*
                     * Нам нужно определить новый список таблиц SDK, чтобы по ним 
                     * провести валидацию новых db.ini.
                     * 
                     * Если мы обрабатываем проект, то SDK-шный db.ini уже готов и 
                     * можем положиться на него. Если мы подготавливаем SDK-шный db.ini,
                     * но новый список таблиц возмём из развёрнутой тестовой БД.
                     */
                    $sdkTableNames = $scope == ENTITY_SCOPE_SDK ? array_keys($tables) : DbIni::getSdkTables();

                    if ($scope == ENTITY_SCOPE_PROJ) {
                        //Уберём из всех таблиц - SDK`шные
                        array_remove_keys($tables, $sdkTableNames);
                    }

                    $scopeTableNames = array_keys($tables);
                    sort($scopeTableNames);

                    /*
                     * Составим список таблиц.
                     * Он нам особенно не нужен, но всёже будем его формировать для наглядности - какие таблицы добавились.
                     */
                    $tablesDi = $DM_AUTO->getDirItem(null, 'tables.txt')->putToFile(implode("\n", $scopeTableNames));
                    dolog('Tables: {} saved to {}', print_r($scopeTableNames, true), $tablesDi->getAbsPath());

                    /*
                     * Загружаем полный список таблиц схемы и на основе имеющихся db.ini файлов строим новые, добавляя/удаляя 
                     * таблицы, добавленные/удалённые из схемы.
                     */
                    $dbIniProps = PsDbIniHelper::makeDbIniForSchema($scope, $tables);
                    dolog('db.ini props: {}', print_r($dbIniProps, true));
                    $dbIniErrors = PsDbIniHelper::validateAndSaveDbIniTableProps($scope, $dbIniProps, $sdkTableNames);
                    if ($dbIniErrors) {
                        PsUtil::raise('db.ini errors for {}: {}', $scope, print_r($dbIniErrors, true));
                    }

                    /*
                     * Для проекта выгружаем данные, хранящиеся в файлах
                     */
                    if ($scope == ENTITY_SCOPE_PROJ) {
                        dolog('Exporting tables data from files');

                        $DM_AUTO->getDirItem('data')->makePath();
                        $AUTO_DATA_SQL = $DI_AUTO_DATA->touch()->getSqlFileBuilder();

                        //Пробегаемся по таблицам
                        foreach (DbIni::getTables() as $tableName) {
                            $table = PsTable::inst($tableName);
                            if ($table->isFilesync()) {
                                $fileData = $table->exportFileAsInsertsSql();
                                if ($fileData) {
                                    dolog(' + {}', $tableName);
                                    $AUTO_DATA_SQL->appendFile($DM_AUTO->getDirItem('data', $tableName, 'sql')->putToFile($fileData));
                                } else {
                                    dolog(' - {}', $tableName);
                                }
                            }
                        }

                        $AUTO_DATA_SQL->save();

                        /*
                         * Вставим данные в тестовую схему
                         */
                        dolog('Inserting data to test schema.');
                        $props->execureShell($DI_AUTO_DATA);
                    }

                    /*
                     * Теперь ещё создадим тестовые объекты.
                     * Мы уверены, что для SDK тестовая часть есть всегда.
                     */
                    $TEST_SCHEMA_SQL = $DM_AUTO->getDirItem('test', 'schema', 'sql')->makePath()->getSqlFileBuilder();
                    if ($scope == ENTITY_SCOPE_PROJ) {
                        dolog('+ SDK TEST PART');

                        //Добавим секцию в лог
                        $TEST_SCHEMA_SQL->appendMlComment('>>> SDK');

                        //CREATE CHEMA SCRIPT
                        $TEST_SCHEMA_SQL->appendFile($SDK->getDirItem('sysobjects/auto/test', 'schema.sql'));

                        //Добавим секцию в лог
                        $TEST_SCHEMA_SQL->appendMlComment('<<< SDK');
                    }
                    $TEST_SCHEMA_SQL->appendFile($DM_SYSOBJECTS->getDirItem('test', 'schema.sql'), false);
                    $TEST_SCHEMA_SQL->appendFile($DM_SYSOBJECTS->getDirItem('test', 'data.sql'), false);
                    $TEST_SCHEMA_SQL->save();

                    /*
                     * На тестовой схеме прогоняем скрипты с тестовыми данными
                     */
                    dolog('Making test schema objects.');
                    $props->execureShell($TEST_SCHEMA_SQL->getDi());
                }
                #end conn== TEST

                /*
                 * Если были сгенерированы данные из файлов - добавляем их
                 */
                if ($DI_AUTO_DATA->isFile() && ($DI_AUTO_DATA->getSize() > 0)) {
                    dolog('Append data inserts to {}', $SCHEMA_SQL->getDi()->getName());
                    $SCHEMA_SQL->appendFile($DI_AUTO_DATA);
                }

                //SAVE .sql
                $SCHEMA_SQL->save();
            }
        }
    }

    dolog('Database schemas successfully exported');
}

//Отключаем автоматический коннект на базу, чтоыб наш генератор ничего ненабедокурил на продуктиве
$PS_NO_AUTO_CONNECT = true;
$CALLED_FILE = __FILE__;
$LOGGERS_LIST[] = 'PsConnectionParams';
require_once dirname(dirname(__DIR__)) . '/MainImportProcess.php';
?>