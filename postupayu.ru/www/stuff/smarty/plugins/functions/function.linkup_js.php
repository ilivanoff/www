<?php

/*
 * Подключает ресурс к странице.
 * 
 * Рекомендуется пользоваться только в том случае, когда нет уверенности в сущестровании ресурса.
 */

function smarty_function_linkup_js($params, Smarty_Internal_Template & $smarty) {
    $params = ArrayAdapter::inst($params);

    $dir = $params->str('dir');
    $name = $params->str('name');

    $di = DirManager::resources('scripts')->getDirItem($dir, $name, 'js');
    echo $di->isFile() ? PsHtml::linkJs($di) : '';
}

?>
