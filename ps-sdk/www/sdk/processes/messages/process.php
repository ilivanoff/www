<?php

/*
 * Мы вополняем следующие действия:
 * 1. Собираем в системе все ...Messages.msgs файлы
 * 2. Проверяем, что в этот список входит тестовый файл, поторые лежит рядов в __DIR__/classes
 * 3. Строим классы-адаптеры для сообщений, располагая их рядом с найденными .msgs файлами
 * 4. первый построенный файл будет для тестового класа, на нём проверяем, что всё работает корректно
 */

function executeProcess(array $argv) {
    $CLASS_PATTERN = file_get_contents(file_path(__DIR__, 'class.txt'));
    $METHOD_PATTERN = file_get_contents(file_path(__DIR__, 'method.txt'));

    /*
     * Название тестового файла.
     * Для проверки мы сначала удалим класс для него, если он был, потом проверим, что за класс был сгенерирован.
     */
    $TEST_MESSAGES_FILE = 'ExampleSdkProcessMessages';

    //Убедимся, что тестовый .msgs существует и удалим тестовый .php файл
    $TEST_PHP_DI = DirItem::inst(__DIR__ . '/classes', $TEST_MESSAGES_FILE, PsConst::EXT_PHP)->remove();
    $TEST_MSGS_DI = DirItem::inst(__DIR__ . '/classes', $TEST_MESSAGES_FILE, PsConst::EXT_MSGS);
    check_condition($TEST_MSGS_DI->isFile(), "File {$TEST_MSGS_DI->getAbsPath()} must exists");

    dolog('Loading all files, ended with Messages.msgs');

    //TODO - после нужно получать эту информацию из какого-нибудь места
    $exceptDirs[] = DirManager::autogen()->relDirPath();
    $exceptDirs[] = DirManager::resources()->relDirPath();
    $exceptDirs[] = DirManager::stuff()->relDirPath();
    //$exceptDirs = array();

    $items = DirManager::inst()->getDirContentFull(null, function(DirItem $di) {
                dolog($di->getAbsPath());
                return $di->isFile() && ends_with($di->getName(), 'Messages.msgs');
            }, $exceptDirs);

    dolog('Message files for processing: {}', count($items));

    //Проверим, что был выбран тестовый файл
    check_condition(in_array($TEST_MSGS_DI, $items, true), "Test file [$TEST_MESSAGES_FILE] is not included");
    //Удалим его из массива...
    array_remove_value($items, $TEST_MSGS_DI, true);
    //И поместим наверх, чтобы он был обработан первым
    array_unshift($items, $TEST_MSGS_DI);

    /* @var $msgsDi DirItem */
    foreach ($items as $msgsDi) {
        LOGBOX('PROCESSING [{}]', $msgsDi->getAbsPath());

        //Сбросим методы
        $METHODS = array();

        //Извлечём название класса
        $CLASS = $msgsDi->getNameNoExt();

        //DirItem файла с сообщениями php
        $classDi = DirItem::inst($msgsDi->getDirname(), $CLASS, PsConst::EXT_PHP);

        //Получаем сообщения из файла .msgs
        $messages = $msgsDi->getFileAsProps();

        foreach ($messages as $MSG_KEY => $MGG_VALUE) {
            dolog(' >> {}={}', $MSG_KEY, $MGG_VALUE);
            //Получим список всех параметров из макросов ({0}, {1}, {2} и т.д.)
            preg_match_all("/\{(.+?)\}/", $MGG_VALUE, $args);
            $args = array_values(array_unique($args[1]));
            sort($args, SORT_NUMERIC);

            $ARGS = array();
            for ($index = 0; $index < count($args); $index++) {
                $arg = $args[$index];
                $lineDescr = PsStrings::replaceWithBraced('[{}] in line [{}={}]', $msgsDi->getRelPath(), $MSG_KEY, $MGG_VALUE);
                check_condition(is_inumeric($arg), "Invalid argument [$arg] for $lineDescr");
                check_condition($index == $args[$index], "Missing index [$index] for $lineDescr");
                $ARGS[] = '$p' . $index;
            }
            $ARGS = join(', ', $ARGS);

            //Добавляем метод
            dolog(" A: {}::{} ({})", $CLASS, $MSG_KEY, $ARGS);

            $PARAMS['SUPPORT_CLASS'] = PsMsgs::getClass();
            $PARAMS['MESSAGE'] = $MGG_VALUE;
            $PARAMS['FUNCTION'] = $MSG_KEY;
            $PARAMS['ARGS'] = $ARGS;
            $METHODS[] = PsStrings::replaceMapBracedKeys($METHOD_PATTERN, $PARAMS);
        }

        dolog('Made methods: ({})', count($METHODS));
        if ($METHODS) {
            $PARAMS['FILE'] = $msgsDi->getAbsPath();
            $PARAMS['CLASS'] = $CLASS;
            $PARAMS['DATE'] = date(DF_PS);
            $PARAMS['METHODS'] = "\n" . join("\n\n", $METHODS) . "\n";

            $CLASS_PHP = PsStrings::replaceMapBracedKeys($CLASS_PATTERN, $PARAMS);

            $classDi->putToFile($CLASS_PHP);
        }

        /*
         * Если обрабатываем тестовый файл - проверим его
         */
        //TEST CLASS VALIDATION START >>>
        if ($msgsDi->equals($TEST_MSGS_DI)) {
            dolog('');
            dolog('Validating test class {}', $TEST_MESSAGES_FILE);

            //Проверим, что .php был сгенерирован
            check_condition($TEST_PHP_DI->isFile(), "File {$TEST_PHP_DI->getAbsPath()} was not created!");

            //Проверим, что для каждого сообщения был создан метод
            $methods = PsUtil::newReflectionClass($TEST_MESSAGES_FILE)->getMethods();
            $messages = $TEST_MSGS_DI->getFileAsProps();

            check_condition(count($methods) == count($messages), 'Methods count missmatch, check ' . $TEST_PHP_DI->getAbsPath());
            /* @var $method ReflectionMethod */
            foreach ($methods as $method) {
                check_condition(array_key_exists($method->getName(), $messages), "No method $TEST_MESSAGES_FILE::" . $method->getName());
            }

            //Проверим, что возвращают методы тестового сгенерированного класса
            function doTest($className, $methodName, array $params, $expected) {
                $method = "$className::$methodName";
                $actual = call_user_func_array($method, $params);
                dolog("{}({})='{}', Expected='{}'", $method, join(', ', $params), $actual, $expected);
                check_condition($actual == $expected, "$actual != $expected");
            }

            doTest($TEST_MESSAGES_FILE, 'message1', array(), 'Message 1');
            doTest($TEST_MESSAGES_FILE, 'message2', array('a', 'b', 'c'), 'Parametred a,c,b');

            dolog('Test class is valid!');
        }
        //TEST CLASS VALIDATION END <<<
    }
    //# DirItems validation end
}

$CALLED_FILE = __FILE__;
$LOGGERS_LIST[] = 'DirFiller';
require_once dirname(dirname(__DIR__)) . '/MainImportProcess.php';
?>