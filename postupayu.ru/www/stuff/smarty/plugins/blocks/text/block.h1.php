<?php

function smarty_block_h1($params, $content, Smarty_Internal_Template & $smarty) {
    if (isEmpty($content))
        return;
    echo "<h1 class=\"colored\">$content</h1>";
}

?>
