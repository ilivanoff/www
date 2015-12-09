<?php

function smarty_block_table($params, $content, Smarty_Internal_Template & $smarty) {
    if (!$content) {
        return; //---
    }

    $params = ArrayAdapter::inst($params);

    $strings = explode("\n", trim($content));

    $res = array();
    foreach ($strings as $str) {
        $items = explode('||', $str);
        if (isEmpty($items)) {
            continue;
        }

        $processed = array();
        foreach ($items as $item) {
            $processed[] = explode('::', trim($item));
        }
        $res[] = $processed;
    }

    PSSmarty::template('common/table.tpl', array('items' => $res, 'class' => $params->str('class')))->display();
}

?>
