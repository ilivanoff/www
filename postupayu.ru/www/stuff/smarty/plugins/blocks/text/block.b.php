<?php

/*
 * <u>Жирный текст</u>
 * 
 * Тэг отменён.
 */

function smarty_block_b($params, $content, Smarty_Internal_Template & $smarty) {
    if (!$content)
        return;

    echo "<span class=\"bold\">$content</span>";
}

?>
