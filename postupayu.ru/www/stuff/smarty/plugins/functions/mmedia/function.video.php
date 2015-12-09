<?php

function smarty_function_video($params, Smarty_Internal_Template & $smarty) {
    $params = ArrayAdapter::inst($params);
    MmediaManager::getInstance()->insertVideo(
            $params->str('dir'), //
            $params->str('name'));

    if (PostFetchingContext::getInstance()->isSetted()) {
        PostFetchingContext::getInstance()->setHasVideo();
    }
}

?>
