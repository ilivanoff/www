<?php

function smarty_block_sortable($params, $content, Smarty_Internal_Template & $smarty) {
    if (!$content) {
        return; //---
    }

    $params = ArrayAdapter::inst($params);

    $sep = $params->str('sep');

    $strings = explode("\n", trim($content));

    $res = array();
    foreach ($strings as $str) {
        if (isEmpty($str)) {
            continue;
        }
        $items = explode('||', $str);
        $res[] = array('l' => trim($items[0]), 'r' => trim($items[1]), 's' => $sep);
    }

    PSSmarty::template('common/sortable.tpl', array('strings' => $res))->display();
}

?>
