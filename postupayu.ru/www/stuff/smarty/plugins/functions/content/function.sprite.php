<?php

function smarty_function_sprite($params, Smarty_Internal_Template & $smarty) {
    /*
     * Название картинки
     */
    $name = value_Array('name', $params);

    $group = value_Array(array('gr', 'group'), $params);
    $group = $group ? $group : CssSpritesManager::DIR_ICO;

    $withGray = array_key_exists('nc', $params);

    echo CssSpritesManager::getDirSprite($group, $name, $withGray);
}
