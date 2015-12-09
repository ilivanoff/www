<?php

/*
 * Скрытый текст
 */

function smarty_block_hidden($params, $content, Smarty_Internal_Template &$template) {
    if (!$content) {
        return;
    }

    $params = ArrayAdapter::inst($params);

    $name = $params->str(array('name', 'text'));
    $toggle = $params->bool('toggle');
    $content = trim($content);

    $tpl = $template->smarty->createTemplate('common/hidden_text.tpl');
    $tpl->assign('name', $name ? $name : 'показать');
    $tpl->assign('toggle', $toggle);
    $tpl->assign('body', $content);
    $tpl->display();
}

?>
