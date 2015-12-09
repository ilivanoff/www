<?php

function smarty_block_tasks($params, $content, Smarty_Internal_Template & $smarty) {
    SmartyBlockContext::getInstance()->registerBlock($content, __FUNCTION__);
    if ($content) {
        echo $content;
    }
}

?>
