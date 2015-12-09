<?php

function smarty_function_binom($params, Smarty_Internal_Template & $smarty) {
    $n = value_Array('n', $params);
    $n = $n === null ? 'n' : $n;
    $m = value_Array('m', $params);
    $m = $m === null ? 'm' : $m;

    echo '<span class="binon_holder">&nbsp;<table class="binom"><tbody><tr><td class="lbr" rowspan="2"></td><td>' . $n . '</td><td class="rbr" rowspan="2"></td></tr><tr><td>' . $m . '</td></tr></tbody></table></span>';
}