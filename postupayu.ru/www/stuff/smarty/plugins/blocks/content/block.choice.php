<?php

function smarty_block_choice($params, $content, Smarty_Internal_Template & $smarty) {
    $ctxtParams = SmartyBlockContext::getInstance()->registerBlock($content, __FUNCTION__);

    if (!$content) {
        return;
    }

    //<select name="$name" /> || <input type="checkbox|readio" name="$name" />
    $name = array_get_value('name', $params);

    //Если передан label, то поле будет отображено, как field формы
    $label = array_get_value('label', $params);

    //Текущее значение
    $curVal = array_get_value_unset('value', $params);

    //Текущее значение
    $choiceType = array_get_value_unset('type', $params);

    //Значения
    $options = array_get_value(SmartyBlockContext::CHOICE_OPTION, $ctxtParams);
    if (isEmpty($options)) {
        return; //---
    }

    switch ($choiceType) {
        case 'combo':
        case 'select':
            echo $label ? PsHtmlForm::select($label, $name, $params, $options, $curVal) : PsHtml::select($params, $options, $curVal);
            break;

        case 'check':
        case 'checkboxes':
            echo $label ? PsHtmlForm::checkboxes($label, $name, $options, $curVal) : PsHtml::checkboxes($name, $options, $curVal);
            break;

        case 'radio':
        case 'radios':
            echo $label ? PsHtmlForm::radios($label, $name, $options, $curVal) : PsHtml::radios($name, $options, $curVal);
            break;

        default:
            echo PsHtml::divErr("Неизвестный тип выбора: [$choiceType]");
    }
}

?>