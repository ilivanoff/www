<?php

// {ex}, {th}

function smarty_block_head($params, $content, Smarty_Internal_Template & $smarty) {
    if ($content) {
        SmartyBlockContext::getInstance()->getParentBlockSetVirtualCtxt(array('ex', 'th'), __FUNCTION__, true);
        SmartyBlockContext::getInstance()->setParam('c_head', trim($content));
        SmartyBlockContext::getInstance()->dropVirtualContext();
    }
}

?>
