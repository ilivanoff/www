<?php

function smarty_block_todo($params, $content, Smarty_Internal_Template & $smarty) {
    if (!$content) {
        return;
    }

    $tpl = PSSmarty::template('common/todo.tpl');
    $tpl->assign('c_todo', trim($content));
    $tpl->display();
}

?>
