<?php

/*
 * <u>Подчёркнутый текст</u>
 * 
 * Тэг отменён.
 */

function smarty_block_u($params, $content, Smarty_Internal_Template & $smarty) {
    if (!$content)
        return;

    echo "<span class=\"underline\">$content</span>";
}

?>
