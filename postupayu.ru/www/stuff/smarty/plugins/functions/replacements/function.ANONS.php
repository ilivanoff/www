<?php

function smarty_function_ANONS($params, Smarty_Internal_Template & $smarty) {
    $replId = PostFetchingContext::REPLACEMENT_ANONS;
    PostFetchingContext::getInstance()->registerReplacement($replId);
    echo $replId;
}

?>
