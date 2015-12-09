<?php

function smarty_block_green($params, $content, Smarty_Internal_Template & $smarty) {
    if ($content) {
        echo "<span class=\"green\">$content</span>";
    }
}

?>
