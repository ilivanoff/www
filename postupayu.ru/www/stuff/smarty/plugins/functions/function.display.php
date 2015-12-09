<?php

/*
 * pattern - шаблон
 * gr - группа, в которой данный шаблон нужно искать
 */

function smarty_function_display($params, Smarty_Internal_Template &$template) {

    foreach ($params as $key => $value) {
        if ($key == 'pattern') {
            continue;
        }

        if (is_object($value)) {
            $template->assignByRef($key, $value);
        } else {
            $template->assign($key, $value);
        }
    }

    $template->smarty->display($params['pattern']);
}

?>
