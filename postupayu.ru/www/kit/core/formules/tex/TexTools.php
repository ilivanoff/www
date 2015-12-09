<?php

/**
 * Утилиты для работы с TeX формулами
 */
final class TexTools {

    /**
     * Заменяет TeX-макросы в тексе на те, которые не будут заменены на картинки:
     * Block:  \[...\] -> $$...$$
     * Inline: \(...\) -> \{...\}
     */
    private static function replaceMacrosToNotImaged($TeX) {
        $TeX = str_replace('\(', '\{', $TeX);
        $TeX = str_replace('\)', '\}', $TeX);
        $TeX = str_replace(array('\[', '\]'), '$$', $TeX);
        return $TeX;
    }

    /**
     * Заменяет TeX-макросы в тексе на те, которые будут заменены на картинки:
     * Block:  $$...$$ -> \[...\]
     * Inline: \{...\} -> \(...\)
     */
    private static function replaceMacrosToImaged($TeX) {
        $TeX = str_replace('\{', '\(', $TeX);
        $TeX = str_replace('\}', '\)', $TeX);
        $TeX = PsStrings::pregReplaceCyclic('/\$\$/', $TeX, array('\[', '\]'));

        return $TeX;
    }

    /**
     * Метод проверяет, присутствует ли в тексте TeX формулы
     */
    public static function hasTex($string) {
        return
                strpos($string, '\(') !== false ||
                strpos($string, '\)') !== false ||
                strpos($string, '\{') !== false ||
                strpos($string, '\}') !== false ||
                strpos($string, '\[') !== false ||
                strpos($string, '\]') !== false ||
                strpos($string, '$$') !== false;
    }

    /**
     * Метод проверяет, является ли переданный текст - блочной формулой.
     * Для этого он должен быть обрамлён соответствующими тегами.
     */
    public static function isBlockFormula($text) {
        $text = trim($text);
        return $text && ((starts_with($text, '\[') && ends_with($text, '\]')) || (starts_with($text, '$$') && ends_with($text, '$$')));
    }

    /**
     * Метод проверяет, является ли переданный текст - внутристрочной формулой.
     * Для этого он должен быть обрамлён соответствующими тегами.
     */
    private static function isInlineFormula($text) {
        $text = trim($text);
        return $text && ((starts_with($text, '\(') && ends_with($text, '\)')) || (starts_with($text, '\{') && ends_with($text, '\}')));
    }

    /**
     * Метод удаляет всё лишнее из формулы, добавляя пробелы вокруг специальных символов.
     * Это сделано потому, что интерпретатор TeX`а считает одинаковыми последовательности:
     * [<][ <][< ][ < ], мы же должны защититься от повторного построения формул.
     * 
     * На вход принимается "чистая" формула, без всяких макросов в начале и конце: 
     * \sqrt{x} \pm 2
     */
    public static function safeFormula($TeX) {
        $TeX = str_replace('<', ' < ', $TeX);
        $TeX = str_replace('>', ' > ', $TeX);
        $TeX = str_replace('&', ' & ', $TeX);
        return normalize_string($TeX);
    }

    /**
     * Метод строит хэш для указанной формулы
     */
    public static function formulaHash($formula) {
        $formula = self::safeFormula($formula);
        return $formula ? md5($formula) : null;
    }

    /**
     * Метод утверждает, что передан валидный хэш формулы
     */
    public static function assertValidFormulaHash($hash) {
        check_condition(PsCheck::isMd5($hash), "Invalid formula hash: [$hash].");
    }

    /**
     * Метод проверяет текст на наличие ошибок в TeX формулах
     * 
     * @param string $text - текст, который будет прверен на налицие ошибок входящих в него формул TeX
     * @param bool $hasTeX - признак, есть ли в тексте вообще формулы TeX
     * @return string - текст ошибки, если она есть
     */
    public static function getTexError($text, &$hasTeX = null) {
        //Тесты показали, что этот способ примерно в 100 раз быстрее, чем пробегаться по всему тексту посимвольно
        $hasTeX = self::hasTex($text);

        if (!$hasTeX) {
            return null;
        }

        $text = self::replaceMacrosToImaged($text);

        if (preg_match('/\\\\\[([ \n]*?)\\\\\]/si', $text)) {
            return TexMessages::texErrorEmptyBlock();
        }

        if (preg_match('/\\\\\(([ \n]*?)\\\\\)/si', $text)) {
            return TexMessages::texErrorEmptyInline();
        }

        $openB = false;
        $openI = false;
        $len = strlen($text);

        for ($index = 0; $index < $len; $index++) {
            $ch = $text[$index];
            $ch2 = '';

            if ($ch == '\\') {

                if (++$index >= $len) {
                    continue; //---
                }

                $ch2 = $text[$index];

                if ($ch2 == '[') {
                    if ($openI) {
                        return TexMessages::texErrorInlineNotClosed();
                    }
                    if ($openB) {
                        return TexMessages::texErrorBlockNotClosed();
                    }
                    $openB = true;
                }

                if ($ch2 == ']') {
                    if ($openI) {
                        return TexMessages::texErrorInlineNotClosed();
                    }
                    if (!$openB) {
                        return TexMessages::texErrorBlockNotOpen();
                    }
                    $openB = false;
                }

                if ($ch2 == '(') {
                    if ($openI) {
                        return TexMessages::texErrorInlineNotClosed();
                    }
                    if ($openB) {
                        return TexMessages::texErrorBlockNotClosed();
                    }
                    $openI = true;
                }

                if ($ch2 == ')') {
                    if ($openB) {
                        return TexMessages::texErrorBlockNotClosed();
                    }
                    if (!$openI) {
                        return TexMessages::texErrorInlineNotOpen();
                    }
                    $openI = false;
                }
            }
        }

        if ($openB) {
            return TexMessages::texErrorBlockNotClosed();
        }

        if ($openI) {
            return TexMessages::texErrorInlineNotClosed();
        }

        return null;
    }

    /**
     * Метод утверждает, что переданный текст содержит валидные формулы TeX`а.
     * Если есть ошибки, то метод выбрасывает исключение, в противном случае возвращает 
     * признак - есть ли в тексте вообще TeX формулы.
     */
    private static function assertTexValid($string) {
        $error = self::getTexError($string, $hasTeX);
        check_condition(!$error, 'Переданный текст содержит ошибки TeX`а: ' . $error);
        return $hasTeX;
    }

    /**
     * Метод вызывается и производит замены в TeX формул посредством вызова $callback.
     * 
     * @param string $string - строка, в которой будет производиться поиск
     * @param callable $callback - функция обратного вызова
     *                 function($original, $content, $isBlock). Пример: function(\[x\], x, true).
     * @param bool $replcaAllToImaged - признак, нужно ли предварительно заменить все формулы
     *                                  на их представление, заменяемое на картинки
     * @return string - строка с произведёнными заменами
     */
    public static function replaceTeX($string, $callback, $replcaAllToImaged) {
        if (isEmpty($string)) {
            return '';
        }

        if (!self::assertTexValid($string)) {
            return $string;
        }

        if ($replcaAllToImaged) {
            $string = self::replaceMacrosToImaged($string);
        }

        $b_callback = function ($matches) use (&$callback) {
                    $formula = TexTools::safeFormula($matches[1]);
                    return call_user_func($callback, "\[$formula\]", $formula, true);
                };
        $string = preg_replace_callback('/\\\\\[(.+?)\\\\\]/si', $b_callback, $string);

        $i_callback = function ($matches) use (&$callback) {
                    $formula = TexTools::safeFormula($matches[1]);
                    return call_user_func($callback, "\($formula\)", $formula, false);
                };
        $string = preg_replace_callback('/\\\\\((.+?)\\\\\)/si', $i_callback, $string);

        return $string;
    }

    /**
     * Метод удаляет из текста пустые формулы
     */
    private function clearEmptyTex($tex) {
        $tex = preg_replace('/\\\\\(([ \n]*?)\\\\\)/si', '', $tex);
        $tex = preg_replace('/\\\\\[([ \n]*?)\\\\\]/si', '', $tex);
        return $tex;
    }

}

?>