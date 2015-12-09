<?php

function smarty_block_whois($params, $content, Smarty_Internal_Template &$template) {
    if ($content) {
        /* @var $whoIsTpl Smarty_Internal_Template */
        $whoIsTpl = $template->smarty->createTemplate('common/whois.tpl');
        $whoIsTpl->assign('c_body', $content);
        $whoIsTpl->display();
    }
}

?>
