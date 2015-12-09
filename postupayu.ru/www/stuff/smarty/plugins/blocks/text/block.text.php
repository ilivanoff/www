<?php

/*
 * Блок текста.
 * 
 * Можно использовать
 * white-space: pre-line;
 */

function smarty_block_text($params, $content, Smarty_Internal_Template & $smarty) {
    if (!$content) {
        return;
    }

    $content = nl2br(trim($content));

    $params['class'] = array_get_value('class', $params, '') . ' text';

    return PsHtml::div($params, $content);
}

?>
