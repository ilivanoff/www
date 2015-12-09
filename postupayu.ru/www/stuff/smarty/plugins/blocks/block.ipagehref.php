<?php

function smarty_block_ipagehref($params, $content, Smarty_Internal_Template &$template) {
    if (!$content) {
        return;
    }

    $content = trim($content);
    $content = $content == '.' ? null : $content;

    $item = array_get_value_unset('item', $params);

    return IdentPagesManager::inst()->getIdentPageHref($item, $params, $content);
}

?>