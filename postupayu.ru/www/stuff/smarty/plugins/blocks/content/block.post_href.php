<?php

function smarty_block_post_href($params, $content, Smarty_Internal_Template & $smarty) {
    if (isEmpty($content)) {
        return;
    }

    /*
     * text - текст ссылки
     * Также может быть вставлен произвольный текст
     */
    $text = trim($content);
    $text = $text == '.' ? null : $text;

    /* @var $post Post */
    $post = value_Array('post', $params);
    $post = $post instanceof PostContentProvider ? $post->getPost() : $post;

    $sub = value_Array('sub', $params);

    $blank = !isEmptyInArray('blank', $params);

    $handler = null;
    if ($post) {
        $handler = Handlers::getInstance()->getPostsProcessorByPostType($post->getPostType());
    } else {
        $id = value_Array(array('id', 'post_id'), $params);
        $ident = value_Array(array('ident', 'post_ident'), $params);

        check_condition($id || $ident, 'Не переданы уникальный код или идентификатор поста.');

        $handler = Handlers::getInstance()->getPostsProcessorByPostType($params['type']);

        $post = $id ? $handler->getPost($id, true) : $handler->getPostByIdent($ident, true);
    }

    /*
     * Выкидываем служебные ключи, а остальное - возвращаем
     */
    unset($params['sub']);
    unset($params['blank']);
    unset($params['post']);
    unset($params['type']);
    unset($params['id']);
    unset($params['post_id']);
    unset($params['ident']);
    unset($params['post_ident']);

    echo $handler->postHref($post, $text, $sub, $params, $blank);
}

?>
