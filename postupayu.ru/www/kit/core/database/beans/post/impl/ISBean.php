<?php

/**
 * Базовый бин для работы с выпусками журнала
 *
 * @author azazello
 */
final class ISBean extends PostsBean {

    protected function PostBeanSettings() {
        return new PostBeanSettings(POST_TYPE_ISSUE, 'issue_post_comments', 'issue_post');
    }

    /** @return ISBean */
    public static function inst() {
        return parent::inst();
    }

}

?>
