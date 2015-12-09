<?php

class PsHtmlForm {

    public static function field($label, $field, $hint = null) {
        //Добавим двоеточие здесь, чтобы оно вошло в hint (ели он передан)
        $label = $label ? "$label:" : '';
        if ($label && $hint) {
            $label = PsHtml::hint($label, $hint, PsHtml::HINT_POS_RIGHT, PsHtml::HINT_TYPE_INFO);
        }
        $label = $label ? "<label>$label</label>" : '';
        return PsHtml::div(array('class' => 'field'), $label . $field);
    }

    public static function textarea($label, $name, $content, $maxlen, $manual, $codemirror, array $attrs = array(), $help = null) {
        $attrs['name'] = $name;
        $attrs['cols'] = 20;
        $attrs['rows'] = 110;

        if (is_numeric($maxlen) && $maxlen > 0) {
            /*
             * ml вместо maxlength используем потому, что некоторые браузеры (например firefox)
             * самостоятельно начинают накладывать ограничение на кол-во вводимых символов.
             * Нам же нужно, чтобы пользователь мог вводить текст и был просто предупреждён о
             * превышениилимита. Валидацию произведёт валидатор форм.
             */
            $attrs['ml'] = 1 * $maxlen;
        }

        if ($manual) {
            $attrs['manual'] = 1;
        }

        if ($codemirror) {
            $attrs['codemirror'] = PsCodemirror::checkType($codemirror);
        }

        /*
         * Выполним unsafe, чтобы, например, текст "&alpha;" не был заменён на "α" в браузере
         */
        $content = UserInputTools::unsafeText($content);
        $textarea = PsHtml::html2('textarea', $attrs, $content);
        return self::field($label, $textarea, $help);
    }

    //<input type="file" name="Filedata" value="">
    public static function file($label, $fieldId, $help = null) {
        return self::field($label, PsHtml::input('file', $fieldId, $help));
    }

    public static function submit($buttons, $canReset) {
        $submits = '';
        foreach (to_array($buttons) as $button) {
            $submits .= PsHtml::input('submit', '', $button, array('class' => 'button'));
        }
        if ($canReset) {
            $submits .= PsHtml::input('reset', '', 'Очистить', array('class' => 'button'));
        }
        return self::field(null, $submits);
    }

    public static function text($label, $fieldId, $value, $attrs = array(), $help = null) {
        return self::field($label, PsHtml::input('text', $fieldId, UserInputTools::unsafeText($value), $attrs), $help);
    }

    public static function textInfo($label, $value, $help = null) {
        return self::field($label, PsHtml::span(array('class' => 'input'), UserInputTools::unsafeText($value)), $help);
    }

    public static function password($label, $fieldId, $value, $help = null) {
        $attrs['autocomplete'] = "off";
        return self::field($label, PsHtml::input('password', $fieldId, $value, $attrs), $help);
    }

    public static function select($label, $fieldId, $selectAttrs = array(), $options = array(), $curVal = null, $hasEmpty = false, $help = null) {
        $selectAttrs['name'] = $fieldId;

        /*
         * Пока не будем комбо-боксы для Да/Нет заменять на радиокнопки
         * 
          if (count($options) == 2) {
          $y = false;
          $n = false;
          foreach ($options as $option) {
          $y = $y || ($option['value'] == 1 && $option['content'] == 'Да');
          $n = $n || ($option['value'] == 0 && $option['content'] == 'Нет');
          }
          if ($y && $n) {
          return self::radios($label, $fieldId, $options, $curVal);
          }
          }
         */

        return self::field($label, PsHtml::select($selectAttrs, $options, $curVal, $hasEmpty), $help);
    }

    public static function radios($label, $fieldId, $radios = array(), $curVal = null, $help = null) {
        return self::field($label, PsHtml::radios($fieldId, $radios, $curVal), $help);
    }

    public static function checkboxes($label, $fieldId, $checkboxes = array(), $curVal = array(), $help = null) {
        return self::field($label, PsHtml::checkboxes($fieldId, $checkboxes, $curVal), $help);
    }

    public static function capture() {
        $img = PsHtml::img(array('id' => PsConstJs::CAPTCHA_IMG_ID, 'alt' => 'Антибот'));
        $input = PsHtml::input('text', FORM_PARAM_PSCAPTURE);
        return self::field(null, $img . '<br />' . $input);
    }

}

?>