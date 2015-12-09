<?php

/*
 * Абзац
 */

function smarty_block_p($params, $content, Smarty_Internal_Template & $smarty) {
    if (isEmpty($content))
        return;

    $styles = array();
    $align = value_Array('al', $params);
    switch ($align) {
        case 'l':
            $styles['text-align'] = 'left';
            break;
        case 'c':
            $styles['text-align'] = 'center';
            break;
        case 'r':
            $styles['text-align'] = 'right';
            break;
    }

    $bold = !isEmptyInArray('bold', $params);
    if ($bold) {
        $styles['font-weight'] = 'bold';
    }

    return PsHtml::p(array('style' => $styles), $content);
}

?>