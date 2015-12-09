<?php

function smarty_block_feed($params, $content, Smarty_Internal_Template &$smarty) {
    if (!$content) {
        return;
    }

    $text = trim($content);
    $text = $text == '.' ? null : $text;

    $blank = !isEmptyInArray('blank', $params);
    $http = !isEmptyInArray('http', $params);

    return FeedbackManager::inst()->writeToUsHref($text, $blank, $http);
}

?>
