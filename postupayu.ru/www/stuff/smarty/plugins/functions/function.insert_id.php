<?php

function smarty_function_insert_id($params, Smarty_Internal_Template & $smarty) {
    $item = value_Array('item', $params);
    if ($item) {
        $ident = IdHelper::ident($item);
        echo "id=\"$ident\"";
    }
}

?>
