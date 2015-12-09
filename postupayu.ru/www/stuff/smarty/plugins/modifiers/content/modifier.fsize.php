<?php

/*
 * На вход - размер файла в байтах
 */

function smarty_modifier_fsize($size, $short = false) {
    if ($size < 1024) {
        echo $short ? "$size бт" : "$size байт";
    } else if ($size < 1024 * 1024) {
        $size = round($size / 1024, 2);
        echo $short ? "$size кб" : "$size килобайт";
    } else {
        $size = round($size / (1024 * 1024), 2);
        echo PsHtml::span(array('class' => 'red'), $short ? "$size мб" : "$size мегабайт");
    }
}

?>
