<?php

// {book}, {task}, {citata}

function smarty_block_from($params, $content, Smarty_Internal_Template & $smarty) {
    if (!$content) {
        return;
    }
    $content = trim($content);

    $parent = SmartyBlockContext::getInstance()->getParentBlock(array('book', 'task', 'citata'), __FUNCTION__, true);

    switch ($parent) {
        case 'book':
            echo "<h6 class=\"book_info\">$content</h6>";
            break;
        default:
            SmartyBlockContext::getInstance()->setVirtualContext($parent);
            SmartyBlockContext::getInstance()->setParam('c_from', $content);
            SmartyBlockContext::getInstance()->dropVirtualContext();
            break;
    }
}

?>