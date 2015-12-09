<?php

/*
 * Декартово поле (виджет)
 */

function smarty_function_dekart($params, Smarty_Internal_Template & $smarty) {
    PSSmarty::template('common/dekart.tpl')->display();
}
