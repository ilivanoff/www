<?php

function smarty_function_PSHOST($params, Smarty_Internal_Template & $smarty) {
    $host = ServerArrayAdapter::HTTP_HOST();
    $ucf = ArrayAdapter::inst($params)->bool('ucf');
    echo $ucf ? ucfirst($host) : $host;
}

?>