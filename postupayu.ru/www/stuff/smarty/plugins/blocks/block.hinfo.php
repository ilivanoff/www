<?php

function smarty_block_hinfo($params, $content, Smarty_Internal_Template &$template) {
    if (!$content) {
        return;
    }

    $content = trim($content);

    $name = value_Array('name', $params);
    $start = value_Array('start', $params);
    $end = value_Array('end', $params);

    $id = value_Array('id', $params);

    $dates = DatesTools::inst()->toString(DatesTools::TS_MONTH_FULL, $start, $end);

    /* @var $hinfoTpl Smarty_Internal_Template */
    $hinfoTpl = $template->smarty->createTemplate('common/human_info.tpl');
    $hinfoTpl->assign('c_id', $id);
    $hinfoTpl->assign('c_name', $name);
    $hinfoTpl->assign('c_date', $dates);
    $hinfoTpl->assign('c_body', $content);
    $hinfoTpl->display();
}

?>
