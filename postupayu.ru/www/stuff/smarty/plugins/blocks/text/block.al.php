<?php

/*
 * Локальная ссылка на странице
 */

function smarty_block_al($params, $content, Smarty_Internal_Template & $smarty) {
    if (!$content) {
        return;
    }

    $id = value_Array('id', $params);
    $id = IdHelper::localId($id);

    $title = value_Array('title', $params);

    $class = value_Array('class', $params);
    $class = $class ? "class='$class'" : '';

    $content = trim($content);
    echo "<a href='#$id' title='$title' $class>$content</a>";
}

?>
