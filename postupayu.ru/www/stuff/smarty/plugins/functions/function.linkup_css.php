<?php

/*
 * Подключает ресурс к странице.
 * 
 * Рекомендуется пользоваться только в том случае, когда нет уверенности в сущестровании ресурса.
 */

function smarty_function_linkup_css($params, Smarty_Internal_Template & $smarty) {
    $params = ArrayAdapter::inst($params);

    $dir = $params->str('dir');
    $name = $params->str('name');
    $media = $params->str('media');

    $di = DirManager::resources('css')->getDirItem($dir, $name, 'css');
    echo $di->isFile() ? PsHtml::linkCss($di, $media) : '';
}

?>
