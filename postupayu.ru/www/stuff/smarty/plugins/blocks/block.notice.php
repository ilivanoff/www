<?php

function smarty_block_notice($params, $content, Smarty_Internal_Template & $smarty) {
    if ($content) {
        echo "<p class=\"notice\">* $content</p>";
    }
}

?>
