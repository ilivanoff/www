<?php

/**
 * Менеджер для работы с текстовыми формулами
 */
final class TextFormulesProcessor {

    /**
     * Метод вызывается до компиляции шаблона и заменяет некоторые математические символы
     * в тексте шаблона.
     */
    public static function replaceMathText($text) {
        //Обернём символы греческого алфавита в специальные span, чтобы изменить их шрифт
        $text = preg_replace(array('/&alpha;/', '/&beta;/', '/&gamma;/', '/&lambda;/', '/&tau;/', '/&mu;/', '/&pi;/'), '<span class="math_text">\\0</span>', $text);

        //Прийдётся заменить эти символы обратно, чтобы не выбивались из разметки.
        $text = preg_replace(array('/&sdot;/', '/&perp;/'), '<span class="main_text">\\0</span>', $text);

        //Заменим наши макросы
        $text = preg_replace('/\\\\sup\{(.+?)\}/si', '<sup>\\1</sup>', $text);
        $text = preg_replace('/\\\\sub\{(.+?)\}/si', '<sub>\\1</sub>', $text);
        $text = preg_replace('/\\\\vect\{(.+?)\}/si', '<span class="math_vector">\\1</span>', $text);
        $text = preg_replace('/\\\\kor\{(.+?)\}/si', '&radic;<span class="math_sqrt">\\1</span>', $text);

        //Разделители
        $text = str_replace('\~', '<span class="text_delim">&nbsp;</span>', $text);

        return $text;
    }

    /**
     * Метод обрабатывает блочные формулы, заключённые в {f}...{/f}, 
     * но не являющиеся формулами TeX (не \[...\]).
     */
    public static function processBlockFormula($content) {
        return PsHtml::div(array('class' => 'block_formula'), normalize_string($content));
    }

}

?>