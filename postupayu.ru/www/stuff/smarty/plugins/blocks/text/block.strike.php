<?php

function smarty_block_strike($params, $content, Smarty_Internal_Template & $smarty) {
    if (isEmpty($content))
        return;

    echo "<span class=\"strike\">$content</span>";
}

?>
