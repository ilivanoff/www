<?php

class PsHtml {

    const COMBO_VALUE = 'value';
    const COMBO_CONTENT = 'content';

    /**
     * Метод возвращает псевдо-id для элемента страницы
     */
    public static function pseudoId() {
        return 'x-' . rand(1000, 100000);
    }

    /**
     * Список атрибутов, которые будут форсированно включены, даже если свойство имеет пустое значение.
     * Так можно поступать с id или title, но нельзя с value, src или href
     */
    private static $CAN_BE_EMPTY = array('value', 'src', 'href', 'alt');

    /**
     * Строит строку параметров, которая будет добавлена к html тегу.
     * 
     * @param array $params - параметры
     * @param string $attrName - название атрибута, под которым будут добавлены значения
     * @param string $attrValue - значение из массива параметров
     * @param callable $callback - функция для преобразования значения из массива в текст для значения свойства
     */
    private static function registerParam(&$params, $attrName, $attrValue, $callback = null) {
        if (is_string($attrValue)) {
            $attrValue = trim($attrValue);
        } else if (is_numeric($attrValue)) {
            //
        } else if ($callback) {
            $attrValue = trim(call_user_func($callback, $attrValue));
        }

        if ($attrValue || ($attrName && ($attrValue === 0 || $attrValue === '0')) || ($attrName && in_array($attrName, self::$CAN_BE_EMPTY))) {
            $params[] = $attrName ? "$attrName=\"$attrValue\"" : $attrValue;
        }
    }

    private static function processAttrs($attrs) {
        $params = array();
        $attrs = to_array($attrs);
        foreach ($attrs as $key => $value) {
            if (!$key || is_numeric($key)) {
                continue;
            }
            #1
            switch ($key) {
                case 'class':
                    self::registerParam($params, $key, $value, function($value) {
                                return concat(array_unique(to_array_expand($value)));
                            });
                    break;

                case 'style':
                    self::registerParam($params, $key, $value, function($value) {
                                $str = '';
                                $value = to_array($value);
                                foreach ($value as $styleName => $styleValue) {
                                    $str .= "$styleName:$styleValue;";
                                }
                                return $str;
                            });
                    break;

                case 'data':
                    self::registerParam($params, null, $value, function($value) {
                                $str = '';
                                $value = to_array($value);
                                foreach ($value as $dataName => $dataValue) {
                                    $str .= " data-$dataName=\"$dataValue\"";
                                }
                                return $str;
                            });
                    break;

                default :
                    self::registerParam($params, $key, $value, function($value) {
                                if ($value instanceof DirItem) {
                                    return $value->getRelPath();
                                }
                                return strval($value);
                            });
                    break;
            }
            #1
        }

        return concat($params);
    }

    public static function html1($tag, $attrs = array()) {
        $attrs = self::processAttrs($attrs);
        return "<$tag $attrs/>";
    }

    public static function html2($tag, $attrs = array(), $content = null) {
        $attrs = self::processAttrs($attrs);
        $content = trim($content);
        return "<$tag $attrs>$content</$tag>";
    }

    /**
     * Атрибуты для html тегов
     */
    public static function styles2string($styles) {
        return self::processAttrs(array('style' => $styles));
    }

    //В блочных smarty-функциях data можно задать так: data='data-param1="a" data-param2="b"'
    public static function data2string($data) {
        return self::processAttrs(array('data' => $data));
    }

    public static function classes2string($classes) {
        return self::processAttrs(array('class' => $classes));
    }

    //<link rel="stylesheet" href="/x/y/z.css" type="text/css" media="all"/>
    public static function linkLink(array $params) {
        if ($params['rel'] == 'shortcut icon') {
            //favicon
        } else {
            $params['rel'] = 'stylesheet';
            $params['type'] = 'text/css';
            $params['media'] = array_get_value('media', $params, 'all');
        }
        return self::html1('link', $params);
    }

    //<link rel="stylesheet" href="/x/y/z.css" type="text/css" media="all"/>
    public static function linkCss($path, $media = 'all') {
        $attrs['rel'] = 'stylesheet';
        $attrs['href'] = $path;
        $attrs['type'] = 'text/css';
        $attrs['media'] = $media ? $media : 'all';
        return self::html1('link', $attrs);
    }

    //<script type="text/javascript" src="/x/y/z.js"></script>
    public static function linkJs($src = null, $content = null, array $attrs = array()) {
        $attrs['type'] = 'text/javascript';
        $src = array_get_value('src', $attrs, $src);
        if ($src) {
            $attrs['src'] = $src;
        } else {
            unset($attrs['src']);
        }
        return self::html2('script', $attrs, $content);
    }

    //<input type="hidden" name="x" value="y" />
    public static function input($type, $name = '', $value = '', $attrs = array()) {
        $attrs['type'] = $type ? $type : 'text';
        $attrs['name'] = $name;
        $attrs['value'] = trim($value);
        return self::html1('input', $attrs);
    }

    //<input type="hidden" name="x" value="x" />
    public static function hidden($name, $value = '') {
        return self::input('hidden', $name, $value);
    }

    //<input type="hidden" name="x" value="x" /><input type="hidden" name="y" value="y" />
    public static function hiddens(array $hiddens) {
        $result = array();
        foreach ($hiddens as $name => $value) {
            $result[] = self::hidden($name, $value);
        }
        return implode('', $result);
    }

    //<img src="/x/y/z.jpg" class="class" style="x:1;" data-x="1" alt="z.jpg"/>
    public static function img(array $attrs = array()) {
        if (!array_key_exists('alt', $attrs)) {
            $src = array_get_value('src', $attrs, '');
            $attrs['alt'] = basename(trim($src instanceof DirItem ? $src->getRelpath() : $src));
        }
        return self::html1('img', $attrs);
    }

    //<span class="x" data-a="y">content</span>
    public static function span(array $attrs = array(), $content = null) {
        return self::html2('span', $attrs, $content);
    }

    public static function spanErr($content, array $attrs = array()) {
        $attrs['class'] = to_array(array_get_value('class', $attrs));
        $attrs['class'][] = 'error';
        return self::span($attrs, $content);
    }

    public static function nobr($content) {
        return self::span(array('class' => 'nowrap'), $content);
    }

    public static function div(array $attrs = array(), $content = null) {
        return self::html2('div', $attrs, $content);
    }

    public static function divErr($content, array $attrs = array()) {
        $attrs['class'] = to_array(array_get_value('class', $attrs));
        $attrs['class'][] = 'info_box';
        $attrs['class'][] = 'err';
        return self::div($attrs, $content);
    }

    public static function p(array $attrs = array(), $content = null) {
        return self::html2('p', $attrs, $content);
    }

    public static function a(array $attrs = array(), $content = null, $blank = false) {
        if ($blank) {
            $attrs['target'] = '_blank';
        }
        return self::html2('a', $attrs, $content);
    }

    public static function gray($content) {
        return self::span(array('class' => 'gray'), $content);
    }

    /*
     * select
     * option (value, content)
     */

    public static function select($selectAttrs = array(), $options = array(), $curVal = null, $hasEmpty = false) {
        $optionsHtml = '';
        $hasSelected = false;
        if ($hasEmpty) {
            array_unshift($options, self::comboOption('', '-- Не выбрано --'));
        }
        foreach ($options as $optionAttr) {
            $value = array_get_value(self::COMBO_VALUE, $optionAttr, '');
            $content = array_get_value_unset(self::COMBO_CONTENT, $optionAttr, '');
            $content = str_replace(' ', '&nbsp;', $content);

            $isSelected = !$hasSelected && (array_key_exists('selected', $optionAttr) || ($value == $curVal));
            if ($isSelected) {
                $hasSelected = true;
                $optionAttr['selected'] = 'selected';
            } else {
                unset($optionAttr['selected']);
            }

            $optionsHtml .= self::html2('option', $optionAttr, $content);
        }
        return self::html2('select', $selectAttrs, $optionsHtml);
    }

    /*
     * 
     * radio | checkbox
     * 
     */

    private static function choice($type, $name, $value = '', $label = null, $attrs = array()) {
        $label = trim($label);
        if (!$label && !array_key_exists('title', $attrs)) {
            $attrs['title'] = $value;
        }

        $attrs['class'] = to_array(array_get_value('class', $attrs));
        $attrs['class'][] = $type;

        $input = self::input($type, $name, $value, $attrs);
        if ($label) {
            $input = "<label>$input $label</label>";
        }
        return $input;
    }

    //<input type="radio" name="group" value="1" />
    public static function radio($name, $value = '', $label = null, $attrs = array()) {
        return self::choice('radio', $name, $value, $label, $attrs);
    }

    //<input type="checkbox" name="group" value="1" />
    public static function checkbox($name, $value = '', $label = null, $attrs = array()) {
        return self::choice('checkbox', $name, $value, $label, $attrs);
    }

    //<input type="radio" name="yesno" value="1" />
    public static function radios($name, $radios = array(), $curVal = null) {
        $radiosHtml = '';
        $hasSelected = false;
        foreach ($radios as $radioAttr) {
            $value = array_get_value('value', $radioAttr, '');
            $content = array_get_value('content', $radioAttr, '');
            $content = str_replace(' ', '&nbsp;', $content);

            $isSelected = !$hasSelected && (array_get_value('checked', $radioAttr) || ($value == $curVal));
            if ($isSelected) {
                $hasSelected = true;
                $radioAttr['checked'] = 'checked';
            } else {
                unset($radioAttr['checked']);
            }
            unset($radioAttr['content']);

            //У каждой кнопки может быть своё имя. Оно будет использовано, если не передано общее имя.
            $rname = $name ? $name : array_get_value('name', $radioAttr);

            $radiosHtml .= self::radio($rname, $value, $content, $radioAttr);
        }
        return $radiosHtml;
    }

    //<input type="radio" name="yesno" value="1" />
    public static function checkboxes($name, $checkboxes = array(), $selected = array()) {
        $checkboxesHtml = '';
        $selected = to_array($selected);
        $name = ensure_ends_with($name, '[]');
        foreach ($checkboxes as $checkboxAttr) {
            $value = array_get_value('value', $checkboxAttr, '');
            $content = array_get_value('content', $checkboxAttr, '');
            $content = str_replace(' ', '&nbsp;', $content);

            if (array_get_value('checked', $checkboxAttr) || in_array($value, $selected)) {
                $checkboxAttr['checked'] = 'checked';
            } else {
                unset($checkboxAttr['checked']);
            }
            unset($checkboxAttr['content']);

            //У каждого флажка может быть своё имя. Оно будет использовано, если не передано общее имя.
            $cname = $name ? $name : array_get_value('name', $checkboxAttr);

            $checkboxesHtml .= self::checkbox($cname, $value, $content, $checkboxAttr);
        }
        return $checkboxesHtml;
    }

    /**
     * HINTS
     */
    //Расположение

    const HINT_POS_TOP = 'top';
    const HINT_POS_RIGHT = 'right';
    const HINT_POS_BOTTOM = 'bottom';
    const HINT_POS_LEFT = 'left';

    //Цвет
    const HINT_TYPE_NORM = '';
    const HINT_TYPE_INFO = 'info';
    const HINT_TYPE_SUCCESS = 'success';
    const HINT_TYPE_WARNING = 'warning';
    const HINT_TYPE_ERROR = 'error';

    public static function hint($content, $hint, $position = self::HINT_POS_TOP, $type = self::HINT_TYPE_NORM, $rounded = true, $always = false, $bounce = false) {
        $attrs['data']['hint'] = html_4show($hint);
        $attrs['class'][] = "hint--$position";
        if ($type) {
            $attrs['class'][] = "hint--$type";
        }
        if ($rounded) {
            $attrs['class'][] = "hint--rounded";
        }
        if ($always) {
            $attrs['class'][] = "hint--always";
        }
        if ($bounce) {
            $attrs['class'][] = "hint--bounce";
        }
        return self::span($attrs, $content);
    }

    /*
     * UTILS
     */

    /**
     * Метод строит массив значений, пригодных для отображения в combo
     */
    public static function comboOption($value, $content, array $other = array()) {
        $other[self::COMBO_VALUE] = array_get_value(self::COMBO_VALUE, $other, $value);
        $other[self::COMBO_CONTENT] = array_get_value(self::COMBO_CONTENT, $other, $content);
        return $other;
    }

}

?>