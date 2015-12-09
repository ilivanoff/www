<?php

function smarty_block_joke($params, $content, Smarty_Internal_Template &$template) {
    if (!$content) {
        return;
    }

    /* @var $jokeTpl Smarty_Internal_Template */
    $jokeTpl = $template->smarty->createTemplate('common/joke.tpl');
    $jokeTpl->assign('text', trim($content));
    $jokeTpl->assign('from', value_Array('from', $params));
    $jokeTpl->display();
}

?>
