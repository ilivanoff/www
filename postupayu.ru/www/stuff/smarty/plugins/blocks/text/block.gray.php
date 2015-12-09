<?php

function smarty_block_gray($params, $content, Smarty_Internal_Template & $smarty) {
    if ($content) {
        echo PsHtml::gray($content);
    }
}

?>
