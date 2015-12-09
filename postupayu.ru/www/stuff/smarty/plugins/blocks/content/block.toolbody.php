<?php

function smarty_block_toolbody($params, $content, Smarty_Internal_Template & $template) {
    if (!$content) {
        return;
    }

    SmartyBlockContext::getInstance()->getParentBlockSetVirtualCtxt('tool', __FUNCTION__, true);
    SmartyBlockContext::getInstance()->setParam(SmartyBlockContext::HAS_TOOL_BODY, true);
    SmartyBlockContext::getInstance()->dropVirtualContext();

    $toolName = value_Array('name', $params);
    if ($toolName) {
        /* @var $toolBodyContentTpl Smarty_Internal_Template */
        $toolBodyContentTpl = $template->smarty->createTemplate("tools/$toolName.tpl");
        $content = $toolBodyContentTpl->fetch();
    }

    echo "<div class=\"tool_body\">$content</div>";
}

?>