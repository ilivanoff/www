<?php

class BlogManager extends RubricsProcessor {

    public function newsTitle() {
        return 'Опубликована заметка на блоге';
    }

    public function postsTitle() {
        return 'Посты блога';
    }

    protected function postTitleImpl() {
        return 'Пост';
    }

    protected function rubricTitleImpl() {
        return 'Рубрика';
    }

    public function coverDims() {
        return '250x';
    }

    public function dbBean() {
        return BPBean::inst();
    }

    public function getPostsListPage() {
        return WebPage::inst(BASE_PAGE_BLOG);
    }

    public function getRubricPage() {
        return WebPage::inst(PAGE_RUBRIC);
    }

    public function getPostPage() {
        return WebPage::inst(PAGE_POST);
    }

    public function getPostType() {
        return POST_TYPE_BLOG;
    }

    /** @return BlogManager */
    public static function inst() {
        return parent::inst();
    }

}

?>