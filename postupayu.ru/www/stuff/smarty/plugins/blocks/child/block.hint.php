<?php

// {task}

function smarty_block_hint($params, $content, Smarty_Internal_Template & $smarty) {
    if ($content) {
        SmartyBlockContext::getInstance()->getParentBlockSetVirtualCtxt(array('task'), __FUNCTION__, true);
        SmartyBlockContext::getInstance()->setParam('c_hint', trim($content));
        SmartyBlockContext::getInstance()->dropVirtualContext();
    }
}

?>
