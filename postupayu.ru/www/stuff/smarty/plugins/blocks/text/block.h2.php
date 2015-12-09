<?php

function smarty_block_h2($params, $content, Smarty_Internal_Template & $smarty) {
    if (isEmpty($content))
        return;
    echo "<h2 class=\"colored\">$content</h2>";
}

?>
