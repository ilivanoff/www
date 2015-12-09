<?php

function smarty_function_gymex_body($params, Smarty_Internal_Template & $template) {
    /* @var $ex GymEx */
    $ex = $params['ex'];
    $exId = $ex->getId();
    $tplPath = "gym/exercises/$exId.tpl";

    if (PSSmarty::smarty()->templateExists($tplPath)) {
        PSSmarty::template($tplPath)->display();
    } else {
//        message_warn("Шаблон для упражнения с кодом [$exId] не найден");
    }
}

?>
