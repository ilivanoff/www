<?php

function smarty_block_box($params, $content, Smarty_Internal_Template & $smarty) {
    if ($content) {
        $params['class'] = to_array(array_get_value('class', $params));
        $params['class'][] = 'psbox';
        return PsHtml::div($params, PsHtml::div(array('class' => 'psboxctt'), $content));
    }
}

?>
