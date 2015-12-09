<?php

function smarty_function_progress($params, Smarty_Internal_Template & $smarty) {
    $PA = ArrayAdapter::inst($params);

    $params['total'] = $PA->int('total');
    $params['current'] = $PA->int('current');
    $params['title'] = $PA->has('title') ? $PA->str('title') : 'Прогресс';

    PSSmarty::template('common/progress.tpl', $params)->display();
}

?>