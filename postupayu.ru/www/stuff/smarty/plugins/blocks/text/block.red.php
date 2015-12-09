<?php

function smarty_block_red($params, $content, Smarty_Internal_Template & $smarty) {
    if ($content) {
        echo "<span class=\"red\">$content</span>";
    }
}

?>
