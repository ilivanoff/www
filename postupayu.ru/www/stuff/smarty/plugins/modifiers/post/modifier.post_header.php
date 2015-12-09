<?php

/**
 * Заголовок поста
 */
function smarty_modifier_post_header(PostContentProvider $postCP, $buildControl = true) {
    return PSSmarty::template('common/post_header.tpl', array('post' => $postCP->getPost(), 'controls' => $buildControl))->fetch();
}

?>