<?php

function smarty_function_gymex_select($params, Smarty_Internal_Template &$template) {
    $groups = GymManager::getInstance()->getGroups();

    /* @var $tpl Smarty_Internal_Template */
    $tpl = $template->smarty->createTemplate('gym/exes_select.tpl');
    $tpl->assignByRef('gym_groups', $groups);
    $tpl->display();
}

?>
