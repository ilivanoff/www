<?php

function smarty_block_tool($params, $content, Smarty_Internal_Template & $template) {
    $ctxtParams = SmartyBlockContext::getInstance()->registerBlock($content, __FUNCTION__);

    if (!$content) {
        return;
    }

    $hasToolBody = !isEmptyInArray(SmartyBlockContext::HAS_TOOL_BODY, $ctxtParams);

    $id = value_Array('id', $params);
    $name = value_Array('name', $params);
    $img = value_Array('img', $params);
    $ident = value_Array('ident', $params);

    /*
     * Вычислим - показывать утилиту как ссылку или нет.
     * Такое возможно в двух случаях: у утилиты есть контент или мы насильно её делаем
     * ссылкой, чтобы "рулить" руками.
     */
    $asHref = $hasToolBody;

    $toolTpl = $template->smarty->createTemplate('common/tool.tpl');

    $toolTpl->assign('id', $id ? 'tool_' . $id : '');
    $toolTpl->assign('name', $name);
    $toolTpl->assign('img', $img);
    $toolTpl->assign('ident', $ident);
    $toolTpl->assign('as_href', $asHref);
    $toolTpl->assign('c_body', $content);
    $toolTpl->display();
}

?>
