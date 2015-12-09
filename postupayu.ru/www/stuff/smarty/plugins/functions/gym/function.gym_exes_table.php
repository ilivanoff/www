<?php

function smarty_function_gym_exes_table($params, Smarty_Internal_Template &$template) {
    $exes = GymManager::getInstance()->getExercises();

    /* @var $tpl Smarty_Internal_Template */
    $tpl = $template->smarty->createTemplate('gym/exercises_table.tpl');
    $tpl->assignByRef('exes', $exes);
    $tpl->display();
}

?>
