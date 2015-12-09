<?php

//Включаем логирование, перенаправляем его в консоль и устанавливаем логгеры
$LOGGING_ENABLED = true;
$LOGGING_STREAM = 2;
$LOGGERS_LIST = array('PROCESS', 'PsLogger');

//Запускаем профилирование
$PROFILING_ENABLED = true;

//Установим глобальный массив, чтобы не получать ошибку в момент попытки стартовать сессию
$_SESSION = array();

require_once dirname(__DIR__) . '/sdk/MainImportAdmin.php';

$CALLED_FILE = null;

function dolog($info = '') {
    call_user_func_array(array(PsLogger::inst('PROCESS'), 'info'), func_get_args());
}

$__logBoxNum = 0;

function LOGBOX($msg) {
    global $__logBoxNum;
    ++$__logBoxNum;
    if ($__logBoxNum > 1) {
        dolog('');
    }
    dolog("$__logBoxNum. $msg");
}

function print_stack(Exception $exception) {
    dolog('');
    dolog("ERROR occured: " . $exception->getMessage());
    foreach ($exception->getTrace() as $num => $stackItem) {
        $str = $num . '# ' . (array_key_exists('file', $stackItem) ? $stackItem['file'] : '') . ' (' . (array_key_exists('line', $stackItem) ? $stackItem['line'] : '') . ')';
        dolog(pad_left('', $num, ' ') . $str);
    }
    die(1);
}

restore_exception_handler();
set_exception_handler('print_stack');

function dimpConsoleLog() {
    global $CALLED_FILE;
    if ($CALLED_FILE) {
        $log = file_path(dirname($CALLED_FILE), get_file_name($CALLED_FILE), 'log');
        $FULL_LOG = PsLogger::controller()->getFullLog();
        $FULL_LOG = mb_convert_encoding($FULL_LOG, 'UTF-8', 'cp866');
        file_put_contents($log, $FULL_LOG);
    }
}

register_shutdown_function('dimpConsoleLog');


/*
 * Возвращает параметры командной строки.
 * Нумерация параметров начинается с единицы.
 */

function getCmdParam($idx = 0, $assert = true) {
    global $argv;
    check_condition(is_array($argv) || !$assert, 'Programm can be runned only from console');
    return array_get_value($idx, to_array($argv));
}

function saveResult2Html($tplName, $params = null, $__DIR__ = __DIR__, $htmlName = 'results.html', $title = null) {
    $tplName = ensure_file_ext($tplName, 'tpl');
    $pageClass = cut_string_end($tplName, '.tpl');
    $body = PSSmarty::template("hometools/$tplName", $params)->fetch();

    $pageParams['title'] = $title == null ? 'Результаты' : $title;
    $pageParams['body'] = $body;
    $pageParams['class'] = $pageClass;
    $html = PSSmarty::template('hometools/page_pattern.tpl', $pageParams)->fetch();

    $htmlName = ensure_file_ext($htmlName, 'html');
    DirItem::inst($__DIR__, $htmlName)->writeToFile($html, true);
}

?>