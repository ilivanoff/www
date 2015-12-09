<?php

define('ADODB_DEBUG', false);

define('UPLOAD_MAX_FILE_SIZE', 1024 * 1024 * 3); /* Максимальный размер загружаемых файлов (в байтах) */

/* Максимальная длина ответа для мозайки */
define('MOSAIC_ANS_MAX_LEN', 150);

//define('PS_DEFAULT_TZ', 'Europe/Minsk');
define('PS_DEFAULT_TZ', 'Europe/Moscow');
//define('PS_DEFAULT_TZ', 'Asia/Dubai');

/* Разделитель директорий */
define('DIR_SEPARATOR', '/');

/* Формат для спрайтов изображений */
define('SYSTEM_IMG_TYPE', 'png');

/* Код тестовой сущности */
define('TEST_ENTITY_ID', -1);

/*
 * Формат даты в системе.
 */
//Базовый шаблон
define('DF_PS_HM', 'd-m-Y H:i');
//Базовый шаблон
define('DF_PS', 'Y-m-d H:i:s');
//Базовый шаблон, сезопасный для использования в кач имени файла
define('DF_FILE', 'Y-m-d_H-i-s');
//MySql
define('DF_MYSQL', 'Y-m-d H:i:s');
//Очки пользователя
define('DF_USER_POINTS', 'j F Yг. H:i:s');
//Форматы даты для новостей
define('DF_NEWS', 'j F Yг.');
//Дата публикации поста
define('DF_POSTS', 'j F Yг. H:i');
//Дата комментария (к посту и обратной связи)
define('DF_COMMENTS', 'j F Y в H:i');
//Дата регистрации пользователя
define('DF_USER_REG_EVENT', 'j F Yг., G:i:s');
//Дата для datepicker
define('DF_JS_DATEPICKER', 'd-m-Y');

/*
 * Параметры, передаваемые в GET
 */
define('GET_PARAM_TYPE', 'type');
define('GET_PARAM_RUBRIC_ID', 'rubric_id');
define('GET_PARAM_POST_ID', 'post_id');
define('GET_PARAM_PAGE_NUM', 'paging');
define('GET_PARAM_PAGE', 'page');
define('GET_PARAM_GOTO_MSG', 'msg_id');

define('GET_PARAM_PLUGIN_IDENT', 'name');

define('POPUP_WINDOW_PARAM', 'window');

define('IDENT_PAGE_PARAM', 'pageident');

/*
 * Признак того, что нужно загрузить данные для элемента временной шкалы
 */
define('TIMELINE_LOADING_MARK', '_tlload_');
/*
 * Параметр ajax действия
 */
define('AJAX_ACTION_PARAM', 'ajax_action');

/*
 * Идентификатор акции
 */
define('STOCK_IDENT_PARAM', 'stock_ident');
define('STOCK_ACTION_PARAM', 'stock_action');

/*
 * Максимальная длина полей ввода с коротким текстом.
 */
define('SHORT_TEXT_MAXLEN', 80);
define('EMAIL_MAXLEN', 80);


define('MD5_STR_LENGTH', 32);

define('PS_MAIL_NO_REPLY', 'no-reply');

/*
 * Пол
 */
define('SEX_BOY', 1);
define('SEX_GIRL', 2);


define('REMIND_CODE_PARAM', 'code');
define('REMIND_CODE_LENGTH', 32);


/*
 * Параметры формы
 */
define('FORM_PARAM_ID', 'form_id');
define('FORM_PARAM_ACTION', 'form_action');
define('FORM_PARAM_BUTTON', 'form_button');


define('FORM_PARAM_FILE', 'Filedata');
define('FORM_PARAM_REG_NAME', 'r_name');
define('FORM_PARAM_REG_MAIL', 'r_mail');
define('FORM_PARAM_REG_SEX', 'r_sex');
define('FORM_PARAM_REG_ABOUT', 'r_about');
define('FORM_PARAM_REG_CONTACTS', 'r_contacts');
define('FORM_PARAM_REG_MSG', 'r_msg');

define('FORM_PARAM_REG_OLD_PASS', 'r_old_pass');
define('FORM_PARAM_REG_PASS', 'r_pass');
define('FORM_PARAM_REG_PASS_CONF', 'r_pass_conf');

define('FORM_PARAM_LOGIN', 'login');
define('FORM_PARAM_PASS', 'password');

define('FORM_PARAM_TIMEZONE', 'timezone');

define('FORM_PARAM_NAME', 'u_name');
define('FORM_PARAM_MAIL', 'email');
define('FORM_PARAM_THEME', 'theme');
define('FORM_PARAM_COMMENT', 'comment');
define('FORM_PARAM_COMMENT_ID', 'comment_id');
define('FORM_PARAM_ENTITY_ID', 'entity_id');
define('FORM_PARAM_PSCAPTURE', 'pscapture');
define('FORM_PARAM_PARENT_ID', 'parent_id');
define('FORM_PARAM_POST_ID', 'post_id');
define('FORM_PARAM_POST_IDENT', 'post_ident');
define('FORM_PARAM_POST_TYPE', 'post_type');
define('FORM_PARAM_YES_NO', 'yesno');

/*
 * Типы постов
 */
define('POST_TYPE_BLOG', 'bp');
define('POST_TYPE_ISSUE', 'is');
define('POST_TYPE_TRAINING', 'tr');


/*
 * Параметры сессии
 */
define('SESSION_ACT_WATCHER_PARAM', 'ps_activity_watcher');
define('SESSION_USER_PARAM', 'ps_user');
define('SESSION_POST_WATCHER_PARAM', 'ps_post_watcher');
define('SESSION_UNLOAD_PARAMS', 'SESSION_UNLOAD_PARAMS');
define('SESSION_AUDIT_ACTION', 'ps_audit');


/*
 * Код дефолтного пользователя - администратора, который есть всегда
 */
define('DEFAULT_ADMIN_USER', 1);
define('DEFAULT_SYSTEM_USER', 2);

/*
 * Действия
 */
define('PS_ACTION_NONE', 0);
define('PS_ACTION_CREATE', 1);
define('PS_ACTION_EDIT', 2);
define('PS_ACTION_DELETE', 3);

/*
 * Страницы
 */
define('BASE_PAGE_MAP', 0);
define('BASE_PAGE_INDEX', 1);
define('BASE_PAGE_MAGAZINE', 2);
define('PAGE_ISSUE', 21);
define('BASE_PAGE_BLOG', 3);
define('PAGE_RUBRIC', 31);
define('PAGE_POST', 32);
define('BASE_PAGE_TRAININGS', 4);
define('PAGE_FILING', 41);
define('PAGE_LESSON', 42);
define('PAGE_LESSON_HOW_TO', 43);
define('BASE_PAGE_UNITS', 5);
define('PAGE_OFFICE', 51);
define('PAGE_PARTITION', 52);
define('PAGE_REGISTRATION', 59);
define('PAGE_PASS_REMIND', 100);
define('BASE_PAGE_FEEDBACK', 6);
define('PAGE_HELPUS', 61);
define('PAGE_POPUP', 7);
define('PAGE_TEST', 8);

define('PAGE_ADMIN', 10);

/*
 * Даты копирайта
 */
define('COPY_DATE_FROM', 2009);
define('COPY_DATE_TO', date('Y'));


/*
 * Тип, куда относится сущность - SDK/PROJECT/COMMON
 */
define('ENTITY_SCOPE_ALL', 'ALL');
define('ENTITY_SCOPE_SDK', 'SDK');
define('ENTITY_SCOPE_PROJ', 'PROJ');

/**
 * Проверка версии php
 */
function is_phpver_is_or_greater($major, $minor) {
    return (PHP_MAJOR_VERSION > $major) || (PHP_MAJOR_VERSION == $major && PHP_MINOR_VERSION >= $minor);
}

/**
 * Функции для получения различной информации о файле.
 * 
 * \dir1\dir2\text.xml
 * Array ( [dirname] => \dir1\dir2 [basename] => text.xml [extension] => xml [filename] => text )
 */
function get_file_extension($path) {
    return pathinfo($path, PATHINFO_EXTENSION);
}

function get_file_name($path) {
    return pathinfo($path, PATHINFO_FILENAME);
}

/**
 * Функция добавляет содержимое к файлу.
 * @return bool - признак, успешно ли прошло добавление.
 */
function file_append_contents($filename, $data) {
    return @file_put_contents($filename, "$data", FILE_APPEND) !== false;
}

/**
 * Метод возвращает содержимое строки файла
 */
function file_get_line_contents($path, $lineNum) {
    check_condition(is_inumeric($lineNum), "Invalid file line number given: [$lineNum]");
    $lineNum = 1 * $lineNum;
    check_condition($lineNum > 0, "Only positive line numbers is alloved, given: [$lineNum]");
    $handle = fopen($path, "r");
    for ($num = 1; !feof($handle); ++$num) {
        $line = fgets($handle);
        if ($num == $lineNum) {
            fclose($handle);
            return $line;
        }
    }
    fclose($handle);
    return null;
}

function html_4show($text) {
    return nl2br(htmlspecialchars(trim($text)));
}

//наш throw new Exception()
function raise_error($message) {
    throw new PException($message);
    die($message);
}

//Наш assert
function check_condition($condition, $message) {
    return isEmpty($condition) ? raise_error($message) : $condition;
}

//Заменяет только первое вхождение подстроки в строку (дополнение к str_replace)
function str_replace_first($search, $replace, $subject) {
    return implode($replace, explode($search, $subject, 2));
}

function starts_with($haystack, $needle) {
    if (is_array($needle)) {
        foreach ($needle as $st) {
            if (starts_with($haystack, $st)) {
                return true;
            }
        }
        return false;
    }

    return $needle === '' || strpos($haystack, $needle) === 0;
}

function ensure_starts_with($haystack, $needle) {
    return starts_with($haystack, $needle) ? $haystack : $needle . $haystack;
}

function cut_string_start($string, $start) {
    $start = to_array($start);
    foreach ($start as $prefix) {
        if (starts_with($string, $prefix)) {
            return substr($string, strlen($prefix), strlen($string));
        }
    }
    return $string;
}

function ends_with($string, $end) {
    if (is_array($end)) {
        foreach ($end as $en) {
            if (ends_with($string, $en)) {
                return true;
            }
        }
        return false;
    }

    return substr($string, - strlen($end)) === $end;
}

function ensure_ends_with($haystack, $needle) {
    return ends_with($haystack, $needle) ? $haystack : $haystack . $needle;
}

function ensure_wrapped_with($haystack, $start, $end) {
    return ensure_ends_with(ensure_starts_with($haystack, $start), $end);
}

function cut_string_end($string, $end) {
    $end = to_array($end);
    foreach ($end as $suffix) {
        if (ends_with($string, $suffix)) {
            return substr($string, 0, strlen($string) - strlen($suffix));
        }
    }
    return $string;
}

function contains_substring($haystack, $needle) {
    if (is_array($needle)) {
        foreach ($needle as $st) {
            if (contains_substring($haystack, $st)) {
                return true;
            }
        }
        return false;
    }
    return strpos($haystack, $needle) !== false;
}

function last_char($str) {
    return strlen($str) > 0 ? $str[strlen($str) - 1] : '';
}

function first_char($str) {
    return strlen($str) > 0 ? $str[0] : '';
}

function first_char_remove($string, $cnt = 1) {
    $cnt = min(array($cnt, strlen($string)));
    return $cnt <= 0 ? $string : substr($string, $cnt, strlen($string));
}

function last_char_remove($string, $cnt = 1) {
    $cnt = min(array($cnt, strlen($string)));
    return $cnt <= 0 ? $string : substr($string, 0, strlen($string) - $cnt);
}

/**
 * Валидирует и возвращает пол. В случае ошибки - null.
 */
function get_sex($sex) {
    $sex = is_numeric($sex) ? (int) $sex : null;
    return is_integer($sex) && in_array($sex, array(SEX_BOY, SEX_GIRL)) ? $sex : null;
}

/**
 * Проверяет, имеет ли файл заданное расширение.
 * Если нет - добавляет его.
 */
function ensure_file_ext($file, $ext) {
    $file = normalize_path($file);
    if (!$file || ends_with($file, DIR_SEPARATOR)) {
        return $file;
    }
    $ext = trim($ext);
    if (!$ext || $ext == '.') {
        return $file;
    }
    $file = cut_string_end($file, '.');
    $ext = ensure_starts_with($ext, '.');
    return ends_with($file, $ext) ? $file : $file . $ext;
}

/**
 * "Нормализует" путь к директории
 * [a///b\\c\\\\d/e/f] -> [a/b/c/d/e/f]
 */
function normalize_path($path, $separator = DIR_SEPARATOR) {
    $path = str_replace('\\', $separator, $path);
    $path = str_replace('/', $separator, $path);
    $double = $separator . $separator;
    while (contains_substring($path, $double)) {
        $path = str_replace($double, $separator, $path);
    }
    return trim($path);
}

function next_level_dir($dirs1, $dirs2 = null, $dirs3 = null, $dirs4 = null) {
    return normalize_path(concat(func_get_args(), DIR_SEPARATOR), DIR_SEPARATOR);
}

function file_path($path, $name, $ext = null) {
    return next_level_dir($path, ensure_file_ext($name, $ext));
}

function unique_from_path($dirs1, $dirs2 = null, $dirs3 = null, $dirs4 = null) {
    return str_replace(DIR_SEPARATOR, '-', cut_string_start(cut_string_end(/**/next_level_dir(func_get_args())/**/, DIR_SEPARATOR), DIR_SEPARATOR));
}

function to_win_path($path) {
    return normalize_path($path, '\\');
}

function ensure_dir_endswith_dir_separator($dirpath) {
    return next_level_dir($dirpath, DIR_SEPARATOR);
}

function ensure_dir_startswith_dir_separator($dirpath) {
    return next_level_dir(DIR_SEPARATOR, $dirpath);
}

function is_valid_file_name($name) {
    return !isEmpty($name) && $name[0] != '.';
}

function substr_to_char($str, $charTo) {
    return substr($str, 0, strpos($str, $charTo));
}

function isEmpty($var) {
    return !isset($var) || empty($var) || (is_string($var) && (!trim($var) || $var == 'null')) || is_null($var) || !$var || count($var) == 0;
}

function isTotallyEmpty($var) {
    return isEmpty($var) && $var !== 0 && $var !== '0';
}

function is_inumeric($var) {
    return is_numeric($var) && is_integer(1 * $var);
}

//Преобразование переменной к массиву
function to_array($data) {
    return is_array($data) ? $data : (isTotallyEmpty($data) ? array() : array($data));
}

//"Разворачивает" массив в один массив
function to_array_expand($values, $takeTotallyEmpty = false, &$result = array()) {
    if (is_array($values)) {
        foreach ($values as $value) {
            to_array_expand($value, $takeTotallyEmpty, $result);
        }
    } else {
        if ($takeTotallyEmpty || !isTotallyEmpty($values)) {
            $result[] = $values;
        }
    }
    return $result;
}

/**
 * Склеивает элементы массива в строку, предварительно его развернув
 * echo concat(array('a', 'b', 'c', array(1, 2, 3), 'x', 'yz'), '|');
 * выводит: a|b|c|1|2|3|x|yz
 */
function concat(/* array */$words, $glue = ' ', $takeTotallyEmpty = false) {
    return implode($glue, to_array_expand($words, $takeTotallyEmpty));
}

/**
 * Метод извлекает значение по ключу
 */
function value_Array($keys, $array, $default = null) {
    $keys = to_array($keys);
    $array = to_array($array);
    foreach ($keys as $key) {
        if (array_key_exists($key, $array)) {
            $value = $array[$key];
            return is_string($value) ? trim($value) : $value;
        }
    }
    return $default;
}

/**
 * Вернётся true, если хоть одно значение будет пустым
 */
function isEmptyInArray($keys, array $array) {
    return isEmpty(value_Array($keys, $array));
}

function array_get_value($key, array $searcharray, $default = null) {
    return array_key_exists($key, $searcharray) ? $searcharray[$key] : $default;
}

function array_diff_full(array $a, array $b) {
    return array_values(array_merge(array_diff($a, $b), array_diff($b, $a)));
}

/**
 * Функций пройдёт по всем ключам $keys и для каждого извлечёт значение из $searcharray.
 * Если на любом из шагов вернётся не массив или не будет значения, соответствующего ключу - вернётся $default.
 */
function array_get_value_in(array $keys, $searcharray, $default = null) {
    if (!is_array($searcharray)) {
        return $default;
    }
    foreach ($keys as $key) {
        if (is_array($searcharray)) {
            $searcharray = array_get_value($key, $searcharray);
        } else {
            return $default;
        }
    }
    return $searcharray;
}

function array_get_value_unset($key, array &$searcharray, $default = null) {
    $val = array_get_value($key, $searcharray, $default);
    unset($searcharray[$key]);
    return $val;
}

function array_has_all_keys($keys, array $searcharray) {
    $keys = to_array($keys);
    foreach ($keys as $key) {
        if (!array_key_exists($key, $searcharray)) {
            return false;
        }
    }
    return true;
}

function array_first_not_existed_key($keys, array $searcharray) {
    $keys = to_array($keys);
    foreach ($keys as $key) {
        if (!array_key_exists($key, $searcharray)) {
            return $key;
        }
    }
    return null;
}

function array_has_one_of_keys($keys, array $searcharray) {
    $keys = to_array($keys);
    foreach ($keys as $key) {
        if (array_key_exists($key, $searcharray)) {
            return true;
        }
    }
    return false;
}

function array_remove_value(array &$array, $values, $strict = false) {
    foreach (array_keys($array, $values, $strict) as $key) {
        unset($array[$key]);
    }
}

function array_remove_keys(array &$array, array $keys) {
    foreach ($keys as $key) {
        unset($array[$key]);
    }
}

function array_filter_keys(array $array, array $removeKeys = null, array $leaveKeys = null) {
    if (empty($removeKeys) && $leaveKeys === null) {
        return $array;
    }
    $result = array();
    foreach ($array as $key => $value) {
        if ($removeKeys !== null && in_array($key, $removeKeys)) {
            continue; //---
        }
        if ($leaveKeys !== null && !in_array($key, $leaveKeys)) {
            continue; //---
        }
        $result[$key] = $value;
    }
    return $result;
}

function br() {
    echo '<br />';
}

function formatDecimal($decimal, $decimals = 2) {
    return number_format($decimal, $decimals, ",", "");
}

function ksort_deep(&$array) {
    if (is_array($array)) {
        ksort($array);
        foreach ($array as &$v) {
            ksort_deep($v);
        }
    }
}

function simple_hash($data) {
    $data = to_array($data);
    ksort_deep($data);
    return md5(serialize($data));
}

function normalize_string($string, $noBlanks = false) {
    if (isEmpty($string))
        return '';

    $string = trim(str_replace(array("\r\n", "\n", "\r"), ' ', $string));

    if ($noBlanks) {
        $string = str_replace(' ', '', $string);
    } else {
        while (strripos($string, '  ')) {
            $string = str_replace('  ', ' ', $string);
        }
    }

    return $string;
}

function lowertrim($str) {
    return ps_strtolower(trim($str));
}

function uppertrim($str) {
    return ps_strtoupper(trim($str));
}

function is_assoc_array($array) {
    //return array_keys($arr) !== range(0, (count($arr) - 1));
    $i = -1;
    foreach ($array as $k => $v) {
        if (++$i !== $k) {
            return true;
        }
    }
    return false;
}

function array_to_string(array $array, $assoc = false) {
    $assoc = $assoc || is_assoc_array($array);
    $result = array();
    foreach ($array as $key => $value) {
        $result[] = ($assoc ? "$key=>" : '') . (is_array($value) ? array_to_string($value, false) : var_export($value, true));
    }
    return '[' . implode(', ', $result) . ']';
}

/*
 * Функции для работы со строками в UTF-8
 */

//Установим внутреннюю кодировку для пакета mb
if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('UTF-8');
}

function ps_strlen($string) {
    return function_exists('mb_strlen') ? mb_strlen($string, 'UTF-8') : strlen($string);
}

function ps_strpos($string, $needle, $offset = 0) {
    return function_exists('mb_strpos') ? mb_strpos($string, $needle, $offset, 'UTF-8') : strpos($string, $needle, $offset);
}

function ps_substr($string, $start = 0, $length = null) {
    return function_exists('mb_substr') ? mb_substr($string, $start, $length, 'UTF-8') : substr($string, $start, $length);
}

function ps_strtoupper($string) {
    return function_exists('mb_strtoupper') ? mb_strtoupper($string, 'UTF-8') : strtoupper($string);
}

function ps_strtolower($string) {
    return function_exists('mb_strtolower') ? mb_strtolower($string, 'UTF-8') : strtolower($string);
}

function ps_substr_count($string, $needle) {
    return function_exists('mb_substr_count') ? mb_substr_count($string, $needle, 'UTF-8') : substr_count($string, $needle);
}

function remove_utf8_bom($text) {
    if ($text) {
        $bom = pack('H*', 'EFBBBF');
        $text = preg_replace("/^$bom/", '', $text);
    }
    return $text;
}

function ps_charat($string, $num) {
    return ps_substr($string, $num, 1);
}

function ensure_strlen($str, $length) {
    return trim(ps_substr(trim($str), 0, $length));
}

function ps_is_upper($str) {
    return $str === ps_strtoupper($str);
}

function ps_is_lower($str) {
    return $str === ps_strtolower($str);
}

/**
 * Обычная функция nl2br не заменяет перенос строки \n на <br />, а лишь добавляет его перед \n.
 */
function nl2brr($string) {
    return preg_replace("/\r\n|\n|\r/", '<br />', $string);
//    return str_replace(array("\r\n", "\n", "\r"), '<br />', $string);
}

function pad_left($string, $pad_length, $pad_string) {
    return str_pad($string, $pad_length, $pad_string, STR_PAD_LEFT);
}

function pad_right($string, $pad_length, $pad_string) {
    return str_pad($string, $pad_length, $pad_string, STR_PAD_RIGHT);
}

function pad_zero_left($string, $pad_length) {
    return pad_left($string, $pad_length, '0');
}

function pad($char, $length) {
    return pad_left('', $length, $char);
}

function array_sift_out(array $source, array $remapKeys, $errIfKeyNotFound = true) {
    $result = array();

    if (empty($source)) {
        return $result;
    }

    foreach ($remapKeys as $key => $newKey) {
        $hasKey = array_key_exists($key, $source);

        if ($errIfKeyNotFound && !$hasKey) {
            raise_error("Key [$key] not found in source " . print_r($source, true));
        }

        if ($hasKey) {
            $result[$newKey] = $source[$key];
        }
    }

    return $result;
}

/**
 * @deprecated - Использовать PsRand
 * 
 * @param type $length
 * @param type $spaceAllowed
 * @param type $maxWordLen
 * @return string
 */
function getRandomString($length = 6, $spaceAllowed = false, $maxWordLen = 10) {
    $validCharacters = 'abcdefghijklmnopqrstuxyvwzABCDEFGHIJKLMNOPQRSTUXYVWZ' . ($spaceAllowed ? ' ' : '');

    $validCharsCount = strlen($validCharacters);

    $result = '';
    $noSpaceLen = 0;
    for ($i = 0; $i < $length; $i++) {
        $index = mt_rand(0, $validCharsCount - 1);
        $char = $validCharacters[$index];
        if ($spaceAllowed) {
            ++$noSpaceLen;
            $result.=$char;
            if ($char == ' ') {
                $noSpaceLen = 0;
            } else if ($noSpaceLen >= $maxWordLen) {
                $noSpaceLen = 0;
                $result .= ' ';
            }
        } else {
            $result .= $char;
        }
    }
    return $result;
}

/**
 * Разбивает и валидирует размеры вида [3x5]
 */
function parse_dim($dim) {
    //Валидация размеров
    $dimArr = explode('x', $dim);
    check_condition(count($dimArr) == 2, "Invalid dim [$dim]");
    $w = $dimArr[0];
    $h = $dimArr[1];
    check_condition((!$w || is_numeric($w)) && (!$h || is_numeric($h)), "Invalid dim [$dim]");
    $w = $w ? 1 * $w : '';
    $h = $h ? 1 * $h : '';
    check_condition((!$w || $w > 0) && (!$h || $h > 0), "Invalid dim [$dim]");
    return array($w, $h);
}

?>