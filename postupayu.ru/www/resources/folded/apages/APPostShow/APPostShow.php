<?php

class AP_APPostShow extends BaseAdminPage {

    public function title() {
        return 'Видимость постов';
    }

    public function buildContent() {
        $PARAMS['uts_php'] = time();
        $PARAMS['uts_db'] = UtilsBean::inst()->getDbUnixTimeStamp();

        $posts = array();
        /* @var $pp PostsProcessor */
        foreach (Handlers::getInstance()->getPostsProcessors() as $pp) {
            $posts[$pp->postsTitle()] = AdminPostsBean::inst()->getAllPosts($pp->dbBean());
        }
        $PARAMS['data'] = $posts;

        echo $this->getFoldedEntity()->fetchTpl($PARAMS);
    }

}

?>