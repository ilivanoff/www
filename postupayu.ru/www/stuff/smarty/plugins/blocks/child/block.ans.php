<?php

// {answers}, {ex}, {task}, {question}

function smarty_block_ans($params, $content, Smarty_Internal_Template & $smarty) {
    if ($content === null) {
        return;
    }

    $parent = SmartyBlockContext::getInstance()->getParentBlockSetVirtualCtxt(array('answers', 'ex', 'task', 'question'), __FUNCTION__, true);

    if ($parent == 'answers') {
        $correct = !isEmptyInArray('correct', $params);
        SmartyBlockContext::getInstance()->addParam(SmartyBlockContext::MULTIPLE_ANSWERS, array($content, $correct));
    } else {
        SmartyBlockContext::getInstance()->setParam('c_ans', $content);
    }

    SmartyBlockContext::getInstance()->dropVirtualContext();
}

?>
