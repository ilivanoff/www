<?php

class StringUtils {

    /**
     * Принимает на вход многострочный комментарий и возвращает массив строк,
     * из которых он состоит. Для данного комментария будет два элемента в массиве.
     */
    public static function parseMultiLineComments($comments) {
        $comments = trim($comments);

        $lines = array();
        foreach (explode("\n", $comments) as $line) {
            $line = trim($line);
            if (!$line || starts_with($line, '/*') || ends_with($line, '*/')) {
                continue;
            }
            if (starts_with($line, '*')) {
                $line = trim(first_char_remove($line));
                if ($line) {
                    $lines[] = $line;
                }
                continue;
            }
        }
        return $lines;
    }

    /**
     * Удаляет комментарии такого вида, как этот
     */
    public static function removeMultiLineComments($text) {
        return preg_replace("/\/\\*[\\s\\S]*?\\*\//", '', $text);
    }

    /**
     * 
     */
    public static function normalizeJsFile($content) {
        $content = self::removeMultiLineComments($content);

        $fileLines = explode("\n", $content);

        $taken = array();

        $scipSwap = array('//', '--');

        $swap = array('{', '}', '}}', '{{', '},', '};', '});', 'else', '}else{', '}else', 'else{', '})', 'return;', 'return{');

        $swap_start_end = array('?', ':', '.', '+', '||', '&&', ',', ';');
        $swap_end = array('{', '(');
        $swap_start = array('}');

        $swap_end = array_merge($swap_end, $swap_start_end);
        $swap_start = array_merge($swap_start, $swap_start_end);

        foreach ($fileLines as $line) {
            $line = trim($line);

            //Обрежем строчный комментарий
            //Нужно быть осторожным - двойной слеш может использоваться (в RegExt например)
            if ($line && contains_substring($line, '//')) {
                $line = trim(substr($line, 0, strpos($line, '//')));
            }

            if (!$line) {
                continue;
            }

            $tmp = normalize_string($line, true);
            $prevTmp = count($taken) > 0 ? normalize_string($taken[count($taken) - 1], true) : null;
            if ($prevTmp !== null && !contains_substring($prevTmp, $scipSwap) && (in_array($tmp, $swap) || ends_with($prevTmp, $swap_end) || starts_with($tmp, $swap_start))) {
                $taken[count($taken) - 1] .= ' ' . $line;
            } else {
                $taken[] = $line;
            }
        }

        return trim(implode("\n", $taken));
    }

    public static function normalizeCssFile($content) {
        $content = self::removeMultiLineComments($content);
        return normalize_string($content);
    }

    public static function normalizeResourceFile($type, $content) {
        switch ($type) {
            case 'css':
                return self::normalizeCssFile($content);
            case 'js':
                return self::normalizeJsFile($content);
        }
        check_condition(false, "Unknown resource type [$type] given for normalization.");
    }

    /**
     * Следующая функция определяет для каждого символа его код и строит массив (код_символа=>кол_во_повторений)
     */
    private static $COUNT_CHARS_CACHE = array();

    public static function getCharsCount($str) {
        $str = ps_strtoupper(normalize_string($str, true));
        if ($str && !array_key_exists($str, self::$COUNT_CHARS_CACHE)) {
            self::$COUNT_CHARS_CACHE[$str] = self::getCountCharsImpl($str);
        }
        return array_get_value($str, self::$COUNT_CHARS_CACHE, array());
    }

    public static function getCommonCharsCount($str1, $str2) {
        $chars1 = self::getCharsCount($str1);
        $chars2 = self::getCharsCount($str2);

        $common = 0;
        if (count($chars1) <= count($chars2)) {
            foreach ($chars1 as $char => $cnt) {
                if (array_key_exists($char, $chars2)) {
                    $common += min(array($cnt, $chars2[$char]));
                }
            }
        } else {
            foreach ($chars2 as $char => $cnt) {
                if (array_key_exists($char, $chars1)) {
                    $common += min(array($cnt, $chars1[$char]));
                }
            }
        }

        return $common;
    }

    public static function getCommonMaxSequenceLen($str1, $str2) {
        $str1 = ps_strtoupper(trim($str1));
        $str2 = ps_strtoupper(trim($str2));
        $len1 = ps_strlen($str1);
        $len2 = ps_strlen($str2);

        if (!$len1 || !$len2) {
            return 0;
        }

        if ($len1 > $len2) {
            $tmp = $str1;
            $str1 = $str2;
            $str2 = $tmp;

            $tmp = $len1;
            $len1 = $len2;
            $len2 = $tmp;
        }

        //Теперь первая строка не длинее второй

        if (ps_strpos($str2, $str1) !== false) {
            return $len1;
        }

        $last = 0;

        for ($i = 0; $i < $len1; $i++) {
            $tmp = '';
            for ($j = 0; $j < $len1 - $i; $j++) {
                $tmp = $tmp . ps_charat($str1, $i + $j);
                if (ps_strpos($str2, $tmp) === false) {
                    break;
                }
                $last = max(array($last, $j + 1));
            }
        }

        return $last;
    }

    private static function getCountCharsImpl($str) {
        $count = array();
        for ($index = 0; $index < ps_strlen($str); $index++) {
            $char = ps_charat($str, $index);
            $count[$char] = 1 + array_get_value($char, $count, 0);
        }
        return $count;
    }

}

?>