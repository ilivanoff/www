<?php

class DG_posts extends BaseDialog {

    protected function getWindowTplSmartyParams() {
        $posts = array();
        /* @var $processor PostsProcessor */
        foreach (Handlers::getInstance()->getPostsProcessors() as $processor) {
            /* @var $post Post */
            foreach ($processor->getPosts() as $post) {
                $posts[IdHelper::ident($post)] = array(
                    'name' => $processor->postTitle($post),
                    'url' => $processor->postUrl($post),
                    'cover' => $processor->getCoverDi($post->getIdent(), '64x64')->getRelPath()
                );
            }
        }

        $params['posts'] = $posts;
        return $params;
    }

    protected function cacheGroup() {
        return PSCache::POSTS();
    }

}

?>