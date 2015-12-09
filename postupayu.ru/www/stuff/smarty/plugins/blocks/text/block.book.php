<?php

/*
 * Цитата из книги
 */

function smarty_block_book($params, $content, Smarty_Internal_Template & $smarty) {
    SmartyBlockContext::getInstance()->registerBlock($content, __FUNCTION__);

    if ($content) {
        echo '<div class="book"><div class="content">' . trim($content) . '</div></div>';
    }
}

?>