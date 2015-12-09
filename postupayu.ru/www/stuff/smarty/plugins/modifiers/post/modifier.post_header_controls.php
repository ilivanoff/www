<?php

/**
 * Управление постом
 */
function smarty_modifier_post_header_controls(/* Post or PostContentProvider */ $post) {
    $post = $post instanceof PostContentProvider ? $post->getPost() : $post;

    $pp = Handlers::getInstance()->getPostsProcessorByPostType($post->getPostType());

    /*
     * Собираем кнопки управления
     */

    $buttons = array();

    $hintClasses = 'hint--top hint--info hint--rounded';

    //Печать поста
    $attrs = array();
    $title = 'Версия для печати';
    $attrs['href'] = '#print';
    $attrs['title'] = $title;
    $attrs['data'] = array('hint' => $title);
    $attrs['class'] = $hintClasses;
    $content = CssSpritesManager::getDirSprite(CssSpritesManager::DIR_ICO, 'print', true);
    $buttons[] = PsHtml::a($attrs, $content);

    //Оригинальный вид поста
    if (PsDefines::isReplaceFormulesWithImages()) {
        $attrs = array();
        $title = 'Просмотр ' . ps_strtolower($pp->postTitle(null, 2)) . ' без замены формул на картинки';
        $attrs['href'] = '#originalView';
        $attrs['title'] = $title;
        $attrs['data'] = array('hint' => $title);
        $attrs['class'] = $hintClasses;
        $content = CssSpritesManager::getDirSprite(CssSpritesManager::DIR_ICO, 'formula', true);
        $buttons[] = PsHtml::a($attrs, $content);
    }

    //Быстрый просмотр постов (перелистывание)
    $attrs = array();
    $title = 'Предыдущий/следующий ' . ps_strtolower($pp->postTitle(null, 1));
    $attrs['href'] = '#prevNextView';
    $attrs['title'] = $title;
    $attrs['data'] = array('hint' => $title);
    $attrs['class'] = $hintClasses;
    $content = CssSpritesManager::getDirSprite(CssSpritesManager::DIR_ICO, 'prev_next', true);
    $buttons[] = PsHtml::a($attrs, $content);

    //Предыдущий пост
    $prevPost = $pp->getPrevPost($post->getId(), false);
    if ($prevPost) {
        $attrs = array();
        $attrs['data']['hint'] = $pp->postTitle($prevPost);
        $attrs['class'] = $hintClasses;
        $content = CssSpritesManager::getDirSprite(CssSpritesManager::DIR_ICO, 'prev_s', true);
        $buttons[] = $pp->postHref($prevPost, $content, null, $attrs);
    }

    //Следующий пост
    $nextPost = $pp->getNextPost($post->getId(), false);
    if ($nextPost) {
        $attrs = array();
        $attrs['data']['hint'] = $pp->postTitle($nextPost);
        $attrs['class'] = $hintClasses;
        $content = CssSpritesManager::getDirSprite(CssSpritesManager::DIR_ICO, 'next_s', true);
        $buttons[] = $pp->postHref($nextPost, $content, null, $attrs);
    }

    return PsHtml::div(array('class' => PsConstJs::POST_HEAD_CONTROLS), implode(' ', $buttons));
}

?>