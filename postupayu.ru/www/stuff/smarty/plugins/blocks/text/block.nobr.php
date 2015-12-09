<?php

function smarty_block_nobr($params, $content, Smarty_Internal_Template & $smarty) {
    if (isEmpty($content))
        return;

    echo '<span class="nowrap">' . $content . '</span>';
}

?>
