<?php

function smarty_block_page_href($params, $content, Smarty_Internal_Template &$smarty) {
    if (isEmpty($content)) {
        return;
    }

    $code = value_Array('code', $params);
    $code = $code ? $code : BASE_PAGE_INDEX;

    $sub = value_Array('sub', $params);
    $title = value_Array('title', $params);
    $classes = value_Array('class', $params);
    $blank = !isEmptyInArray('blank', $params);
    $http = !isEmptyInArray('http', $params);

    $urlParams = array();
    foreach ($params as $key => $val) {
        if (starts_with($key, 'p_')) {
            $urlParams[substr($key, 2)] = $val;
        }
    }



    $content = trim($content);
    $content = $content == '.' ? null : $content;

    return WebPage::inst($code)->getHref($content, $blank, $classes, $http, $urlParams, $sub, $title);
}

?>
