<?php

function smarty_modifier_post_content_showcase(PostContentProvider $cp) {
    if ($cp->getPost()->is(POST_TYPE_ISSUE)) {
        return SmartyModifiers::post_anons_placeholder($cp);
    }
    return $cp->getPostContentShowcase()->getContent();
}

?>
