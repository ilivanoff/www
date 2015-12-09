<?php

function smarty_block_h5($params, $content, Smarty_Internal_Template & $smarty) {
    if (isEmpty($content))
        return;
    echo "<h5 class=\"colored\">$content</h5>";
}

?>
