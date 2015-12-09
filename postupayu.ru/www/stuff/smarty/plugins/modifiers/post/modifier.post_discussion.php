<?php

/*
 * Время публикации, кол-во просмотров и т.д.
 */

function smarty_modifier_post_discussion(PostContentProvider $postCP, $limited = true) {
    $post = $postCP->getPost();
    echo Handlers::getInstance()->getCommentsProcessorByPostType($post->getPostType())->buildDiscussion($post->getId(), $limited);
}

?>