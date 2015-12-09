<?php

function smarty_block_psplugin($params, $content, Smarty_Internal_Template & $smarty) {
    $params = ArrayAdapter::inst($params);
    $ident = $params->str(GET_PARAM_PLUGIN_IDENT);
    echo PluginsManager::inst()->buildFromTag($ident, $content, $params);
}

?>
