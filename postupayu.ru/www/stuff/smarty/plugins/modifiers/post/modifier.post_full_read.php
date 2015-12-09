<?php

/*
 * Ссылка на полный просмотр поста
 * + 
 * время публикации и кол-во просмотров
 */

function smarty_modifier_post_full_read(PostContentProvider $postCP) {
    $post = $postCP->getPost();
    $type = $post->getPostType();

    $text = 'Читать полностью &raquo;';

    switch ($type) {
        case POST_TYPE_ISSUE:
            $text = 'Читать выпуск полностью &raquo;';
            break;
        case POST_TYPE_TRAINING:
            $text = 'Перейти к занятию &raquo;';
            break;
    }

    $tpl = PSSmarty::template('post/full_read.tpl');
    $tpl->assign('text', $text);
    $tpl->assign('post', $post);
    $tpl->display();
}

?>
