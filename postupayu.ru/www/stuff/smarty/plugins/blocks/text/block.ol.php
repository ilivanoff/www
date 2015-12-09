<?php

/*
 * Список. Удобен тем, что можно просто писать текст, который сам будет
 * разбит на строки и представлен в виде списка.
 * 
 * Пример:

  {ol start='0' class='my-list' style='color:green;font-weight:bold;' data='data-param1="a" data-param2="b"'}
  Line 1
  Line 2
  Line 3
  {/ol}

 * Параметр start отвечает за номер, с которого начинается нумерация.
 */

function smarty_block_ol($params, $content, Smarty_Internal_Template & $smarty) {
    if (isEmpty($content)) {
        return; //---
    }

    $params['class'] = to_array(array_get_value('class', $params));
    $params['class'][] = 'block_ol';

    $strings = explode("\n", trim($content));

    $lis = array();
    foreach ($strings as $string) {
        if (!isEmpty($string)) {
            $lis[] = '<li>' . trim($string) . '</li>';
        }
    }
    $content = join('', $lis);

    return PsHtml::html2('ol', $params, $content);
}

?>