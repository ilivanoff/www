<?php

/*
 * Время публикации, кол-во просмотров и т.д.
 */

function smarty_modifier_post_meta(PostContentProvider $postCP, $news = false) {
    $post = $postCP->getPost();

    $postId = $post->getId();
    $type = $post->getPostType();

    $pp = Handlers::getInstance()->getPostsProcessorByPostType($type);
    $rp = Handlers::getInstance()->getRubricsProcessorByPostType($type, false);


    $trStateToggle = null;
    $rubHref = null;
    $commentsHref = null;
    $name = null;

    if ($news) {
        $name = $post->getName();
        $name = "<span class=\"name\">$name</span>";
    }

    if (!$news && $post->getPostType() == POST_TYPE_TRAINING && AuthManager::isAuthorized()) {
        $trStateToggle = '<a href="#' . $postId . '" title="Изменить состояние урока" class="toggle">Пройден</a>';
    }

    $rubHref = $rp ? $rp->rubricHref($post->getRubricId(), null, 'rubric') : null;

//    if (!$news) {
    $commentsCnt = $post->getCommentsCount();
    $commentsHref = $pp->postHref($post, "Комментариев: $commentsCnt", 'comments', array('class' => 'commentcount'));
//    }

    if ($name || $trStateToggle || $rubHref || $commentsHref) {
        echo '<div class="post_meta">';
        echo "$name&nbsp;$trStateToggle $rubHref $commentsHref";
        echo '</div>';
    }
}

?>
