<?php

// {choice}

function smarty_block_option($params, $content, Smarty_Internal_Template & $smarty) {
    if ($content === null) {
        return;
    }

    $params['content'] = $content;

    SmartyBlockContext::getInstance()->getParentBlockSetVirtualCtxt('choice', __FUNCTION__, true);
    SmartyBlockContext::getInstance()->addParam(SmartyBlockContext::CHOICE_OPTION, $params);
    SmartyBlockContext::getInstance()->dropVirtualContext();
}

?>
