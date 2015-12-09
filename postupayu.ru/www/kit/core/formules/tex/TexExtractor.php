<?php

/**
 * Класс для извлечения TeX формул из текста.
 *
 * @author azazello
 */
final class TexExtractor {

    private $originalText;            //Текст \[\pi\] с \(\pi\) формулами \(\sqrt{x}\)
    private $maskedText;              //Текст !TEX[0]! с !TEX[1]! формулами !TEX[2]!
    private $texMacroses = array();   //array('\[\pi\]'=>1, '\(\pi\)'=>2, '\(\sqrt{x}\)'=>3)
    private $texContents = array();   //array('\pi', '\sqrt{x}')

    /*
     * 
     */

    /**
     * Метод создаёт экземпляр класса, хранящего в себе информацию о формулах, входящих в строку текста.
     * 
     * @param type $string
     * @param type $replcaAllToImaged
     * @return TexExtractor
     */
    public static function inst($string, $replcaAllToImaged) {
        $extractor = new TexExtractor();
        $extractor->originalText = $string;
        $extractor->maskedText = TexTools::replaceTeX($string, array($extractor, '_extract'), $replcaAllToImaged);
        return $extractor;
    }

    public function _extract($original, $content, $isBlock) {
        if (!$content) {
            return '';
        }
        if (!in_array($content, $this->texContents)) {
            $this->texContents[] = $content;
        }
        if (array_key_exists($original, $this->texMacroses)) {
            return $this->texMacros($this->texMacroses[$original]);
        }
        return $this->texMacros($this->texMacroses[$original] = count($this->texMacroses));
    }

    public function restoreMasks($maskedText) {
        foreach ($this->texMacroses as $tex => $texIdx) {
            $maskedText = str_replace($this->texMacros($texIdx), $tex, $maskedText);
        }
        return $maskedText;
    }

    private function texMacros($num) {
        return "!TEX[$num]!";
    }

    /**
     * Оригинальный текст с формулами, как есть:
     * Текст \[\pi\] с \(\pi\) формулами \(\sqrt{x}\)
     */
    public function getOriginalText() {
        return $this->originalText;
    }

    /**
     * Текст, в котором каждая формула заменена на макрос (тип формулы учитывается):
     * Текст !TEX[0]! с !TEX[1]! формулами !TEX[2]!
     */
    public function getMaskedText() {
        return $this->maskedText;
    }

    /**
     * Массив формул (без учёта типа):
     * array('\pi', '\sqrt{x}')
     */
    public function getTexContents() {
        return $this->texContents;
    }

    /**
     * Массив формул (с учётом типа):
     * array('\[\pi\]', '\(\pi\)', '\(\sqrt{x}\)')
     */
    public function getTexOriginals() {
        return array_keys($this->texMacroses);
    }

}

?>
