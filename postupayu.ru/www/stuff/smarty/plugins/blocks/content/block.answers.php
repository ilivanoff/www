<?php

function smarty_block_answers($params, $content, Smarty_Internal_Template & $smarty) {
    $ctxtParams = SmartyBlockContext::getInstance()->registerBlock($content, __FUNCTION__);

    if (!$content) {
        return;
    }

    $answers = value_Array(SmartyBlockContext::MULTIPLE_ANSWERS, $ctxtParams);
    if ($answers) {
        PSSmarty::template('common/answers.tpl', array('answers' => $answers))->display();
    }
}

?>
