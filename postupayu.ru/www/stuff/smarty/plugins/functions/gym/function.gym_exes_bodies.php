<?php

function smarty_function_gym_exes_bodies($params, Smarty_Internal_Template &$template) {
    $exes = GymManager::getInstance()->getExercises();


    /* @var $tpl Smarty_Internal_Template */
    $tpl = $template->smarty->createTemplate('gym/exercises_bodies.tpl');
    $tpl->assignByRef('gym_exercises', $exes);
    $tpl->display();
}

?>
