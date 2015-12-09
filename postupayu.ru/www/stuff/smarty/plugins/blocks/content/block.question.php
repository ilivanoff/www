<?php

function smarty_block_question($params, $content, Smarty_Internal_Template &$template) {
    $ctxtParams = SmartyBlockContext::getInstance()->registerBlock($content, __FUNCTION__);

    if (!$content) {
        return;
    }

    /* @var $questionTpl Smarty_Internal_Template */
    $questionTpl = $template->smarty->createTemplate('common/question.tpl', $ctxtParams);
    $questionTpl->assign('c_body', $content);
    $questionTpl->display();
}

?>
