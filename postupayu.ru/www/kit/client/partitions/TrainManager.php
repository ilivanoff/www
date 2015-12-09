<?php

class TrainManager extends RubricsProcessor {

    public function newsTitle() {
        return 'Состоялось занятие кружка';
    }

    public function postsTitle() {
        return 'Занятия кружка';
    }

    protected function postTitleImpl() {
        return 'Урок';
    }

    protected function rubricTitleImpl() {
        return 'Раздел';
    }

    public function coverDims() {
        return '210x';
    }

    public function dbBean() {
        return TRBean::inst();
    }

    public function getPostsListPage() {
        return WebPage::inst(BASE_PAGE_TRAININGS);
    }

    public function getRubricPage() {
        return WebPage::inst(PAGE_FILING);
    }

    public function getPostPage() {
        return WebPage::inst(PAGE_LESSON);
    }

    public function getPostType() {
        return POST_TYPE_TRAINING;
    }

    function toggleLessonState($post_id) {
        $this->assertPostExists($post_id);
        TRBean::inst()->toggleLessonState(AuthManager::getUserId(), $post_id);
    }

    public function getUserPassedLessons() {
        $result = array();
        if (AuthManager::isAuthorized()) {
            foreach (TRBean::inst()->getPassedLessons(AuthManager::getUserId()) as $id) {
                $result["tr$id"] = true;
            }
        }
        return $result;
    }

    /** @return TrainManager */
    public static function inst() {
        return parent::inst();
    }

}

?>