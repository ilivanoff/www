<?php

function smarty_modifier_post_content(PostContentProvider $cp) {
    echo $cp->getPostContent()->getContent();
}

?>
