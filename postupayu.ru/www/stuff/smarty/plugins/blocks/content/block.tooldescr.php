<?php

function smarty_block_tooldescr($params, $content, Smarty_Internal_Template & $template) {
    if (isEmpty($content))
        return;

    $content = trim($content);
    $content = nl2br($content);

    echo "<div class=\"tool_descr\">$content</div>";
}

?>
