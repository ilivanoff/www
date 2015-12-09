<?php

function smarty_function_gym_exes_covers($params, Smarty_Internal_Template &$template) {
    $exes = GymManager::getInstance()->getExercises();

    /* @var $tpl Smarty_Internal_Template */
    $tpl = $template->smarty->createTemplate('gym/exercises_covers.tpl');
    $tpl->assignByRef('exes', $exes);
    $tpl->display();
}

?>
