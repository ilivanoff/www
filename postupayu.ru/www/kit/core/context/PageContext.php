<?php

/**
 * Контекст выполнения страницы
 */
class PageContext extends AbstractSingleton {

    /** @return WebPage */
    public function getPage() {
        return WebPages::getCurPage();
    }

    public function getPageCode() {
        return $this->getPage()->getCode();
    }

    public function getPageType() {
        return $this->getPage()->getBuilderType();
    }

    public function isIt($page) {
        return $this->getPage()->isIt($page);
    }

    /*
     * Тип страницы
     */

    public function isAjax() {
        return $this->isAjax || ServerArrayAdapter::IS_AJAX();
    }

    public function isBasicPage() {
        return $this->getPage()->isType(PB_basic::getIdent());
    }

    public function isAdminPage() {
        return $this->getPage()->isType(PB_admin::getIdent());
    }

    public function isPopupPage() {
        return $this->getPage()->isType(PB_popup::getIdent());
    }

    public function isTestPage() {
        return $this->getPage()->isType(PB_test::getIdent());
    }

    /*
     * Функция вернёт текуший тип поста для страниц, на которой показываются:
     * 1. все посты
     * 2. посты текущей рубрики
     * 3. конкретный пост
     */

    public function getPostType() {
        switch ($this->getPageCode()) {
            case BASE_PAGE_MAGAZINE:
            case PAGE_ISSUE:
                return POST_TYPE_ISSUE;
            case BASE_PAGE_BLOG:
            case PAGE_RUBRIC:
            case PAGE_POST:
                return POST_TYPE_BLOG;
            case BASE_PAGE_TRAININGS:
            case PAGE_FILING:
            case PAGE_LESSON:
                return POST_TYPE_TRAINING;
        }
        return null;
    }

    public function isPostsListPage() {
        switch ($this->getPageCode()) {
            case BASE_PAGE_MAGAZINE:
            case BASE_PAGE_BLOG:
            case BASE_PAGE_TRAININGS:
                return true;
        }
        return false;
    }

    public function isPostPage() {
        switch ($this->getPageCode()) {
            case PAGE_ISSUE:
            case PAGE_POST:
            case PAGE_LESSON:
                return true;
        }
        return false;
    }

    public function isRubricPage() {
        switch ($this->getPageCode()) {
            case PAGE_RUBRIC:
            case PAGE_FILING:
                return true;
        }
        return false;
    }

    /** @return PostsProcessor */
    public function getPostProcessor() {
        $postType = $this->getPostType();
        return $postType ? Handlers::getInstance()->getPostsProcessorByPostType($postType, false) : null;
    }

    /** @return RubricsProcessor */
    public function getRubricsProcessor() {
        $postType = $this->getPostType();
        return $postType ? Handlers::getInstance()->getRubricsProcessorByPostType($postType, false) : null;
    }

    /** @return Post */
    public function getPost() {
        $processor = $this->getPostProcessor();
        return $processor ? $processor->getCurrentPost() : null;
    }

    /** @return Rubric */
    public function getRubric() {
        $processor = $this->getRubricsProcessor();
        return $processor ? $processor->getCurrentRubric() : null;
    }

    public function getPostId() {
        $curPost = $this->getPost();
        return $curPost ? $curPost->getId() : null;
    }

    public function getPostIdent() {
        $curPost = $this->getPost();
        return $curPost ? $curPost->getIdent() : null;
    }

    public function getRubricId() {
        $curRubric = $this->getRubric();
        return $curRubric ? $curRubric->getId() : null;
    }

    public function getRubricIdent() {
        $curRubric = $this->getRubric();
        return $curRubric ? $curRubric->getIdent() : null;
    }

    private $requestUrl;

    public function getRequestUrl() {
        if (!isset($this->requestUrl)) {
            $this->requestUrl = $this->getPage()->getPath();

            $GET_PARAMS = array();

            if ($this->isRubricPage()) {
                $GET_PARAMS[GET_PARAM_RUBRIC_ID] = $this->getRubricId();
            }

            if ($this->isPostPage()) {
                $GET_PARAMS[GET_PARAM_POST_ID] = $this->getPostId();
            }

            if ($this->isPopupPage()) {
                $GET_PARAMS = array_merge($GET_PARAMS, PopupPagesManager::inst()->getRequestParams());
            }

            ksort($GET_PARAMS);
            $this->requestUrl = PsUrl::addParams($this->requestUrl, $GET_PARAMS);
        }
        return $this->requestUrl;
    }

    /**
     * Ajax
     */
    private $isAjax;

    public function setAjaxContext() {
        $this->isAjax = true;
    }

    /** @return PageContext */
    public static function inst() {
        return parent::inst();
    }

}

?>