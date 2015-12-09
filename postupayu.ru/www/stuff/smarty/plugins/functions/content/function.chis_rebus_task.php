<?php

/*
 * {chis_rebus_task text='спорт+спорт=кросс'}
 */

function smarty_function_chis_rebus_task($params, Smarty_Internal_Template & $template) {
    /* @var $crtTpl Smarty_Internal_Template */
    $crtTpl = $template->smarty->createTemplate('common/chis_rebus_task.tpl');
    $crtTpl->assign('rebus_txt', value_Array('text', $params));
    $crtTpl->assign('reset', value_Array('reset', $params));
    $crtTpl->display();
}
?>

