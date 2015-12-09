<?php

function smarty_function_doc_href($params, Smarty_Internal_Template & $smarty) {
    $doc = value_Array('doc', $params);
    $text = value_Array('text', $params);
    $title = value_Array('title', $params);
    echo "<a href=\"/resources/docs/$doc\" title=\"$title\" target=\"_blank\">$text</a>";
}

?>
