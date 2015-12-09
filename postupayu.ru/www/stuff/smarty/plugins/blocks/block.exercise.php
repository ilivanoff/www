<?php

function smarty_block_exercise($params, $content, Smarty_Internal_Template &$template) {
    if (isEmpty($content)) {
        return;
    }

    $id = value_Array('id', $params);

    /* @var $ex GymEx */
    $ex = GymManager::getInstance()->getExercise($id);

    $content = trim($content);

    $name = value_Array('name', $params);
    $name = $name ? $name : ($ex ? $ex->getName() : '');

    $class = $ex ? GymManager::getInstance()->getClass($ex) : '';

    $exTemplate = $template->smarty->createTemplate('gym/exercise.tpl');
    $exTemplate->assign('c_id', IdHelper::gymExId($id));
    $exTemplate->assign('c_name', $name);
    $exTemplate->assign('c_class', $class);
    $exTemplate->assign('c_body', $content);
    $exTemplate->display();
}

?>
