<?php

/**
 * Базовый бин для работы с постами блога
 *
 * @author azazello
 */
final class BPBean extends RubricsBean {

    protected function PostBeanSettings() {
        return new PostBeanSettings(POST_TYPE_BLOG, 'blog_post_comments', 'blog_post', 'blog_rubric');
    }

    /** @return BPBean */
    public static function inst() {
        return parent::inst();
    }

}

?>