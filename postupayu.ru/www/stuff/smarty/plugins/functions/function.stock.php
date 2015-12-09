<?php

/*
 * Акции. Задача функции - просто вызвать StockManager, чтобы он смог зарегистрировать акцию.
 */

function smarty_function_stock($params, Smarty_Internal_Template &$template) {
    echo StockManager::inst()->registerStock(ArrayAdapter::inst($params));
}

?>
