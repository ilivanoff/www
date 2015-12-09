<?php

/*
 * nl2br();
 */

function smarty_block_nl2br($params, $content, Smarty_Internal_Template & $smarty) {
    if (isEmpty($content))
        return;
    echo nl2br(trim($content));
}

?>
