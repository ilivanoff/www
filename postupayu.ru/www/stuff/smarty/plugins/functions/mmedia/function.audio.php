<?php

function smarty_function_audio($params, Smarty_Internal_Template & $smarty) {
    $params = ArrayAdapter::inst($params);

    MmediaManager::getInstance()->insertAudio(
            $params->str('dir'), //
            $params->str('name'));
}

?>
