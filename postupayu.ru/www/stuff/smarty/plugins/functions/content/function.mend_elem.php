<?php

function smarty_function_mend_elem($params, Smarty_Internal_Template & $smarty) {
    $num = value_Array('num', $params);
    echo MendeleevManager::getInstance()->getHtml($num);
}