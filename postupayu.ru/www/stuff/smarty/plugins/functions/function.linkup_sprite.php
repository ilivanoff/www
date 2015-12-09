<?php

/*
 * Подключает ресурс к странице.
 * 
 * Рекомендуется пользоваться только в том случае, когда нет уверенности в сущестровании ресурса.
 */

function smarty_function_linkup_sprite($params, Smarty_Internal_Template & $smarty) {
    echo PsHtml::linkCss(CssSpritesManager::getSprite($params['name'])->getCssDi());
}

?>
