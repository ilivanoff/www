<?php

function smarty_block_citata($params, $content, Smarty_Internal_Template &$template) {
    $ctxtParams = SmartyBlockContext::getInstance()->registerBlock($content, __FUNCTION__);

    if (!$content) {
        return;
    }

    $citatas = SmartyBlockContext::getInstance()->hasParentBlock('citatas');

    $from = value_Array('c_from', $ctxtParams);
    $from = $from ? $from : value_Array('from', $params);

    /* @var $citataTpl Smarty_Internal_Template */
    $citataTpl = $template->smarty->createTemplate('common/citata.tpl');
    $citataTpl->assign('child', $citatas);
    $citataTpl->assign('body', trim($content));
    $citataTpl->assign('from', $from);
    $citataTpl->display();
}

?>
