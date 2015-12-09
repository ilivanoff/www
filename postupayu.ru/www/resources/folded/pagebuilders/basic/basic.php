<?php

class PB_basic extends AbstractPageBuilder {

    public static function registerWebPages() {
        /* @var $class BasicPage */
        foreach (BasicPagesManager::inst()->getAccessibleClassNames() as $class) {
            $class::registerWebPages();
        }
    }

    /** @var BasicPage */
    private $basicPage;

    protected function doProcess(PageContext $ctxt, RequestArrayAdapter $requestParams, ArrayAdapter $buildParams) {
        $this->basicPage = BasicPagesManager::inst()->getPage($ctxt->getPage()->getPathBase());
        $this->basicPage->checkAccess();
        $this->basicPage->doProcess($requestParams);
    }

    protected function doBuild(PageContext $ctxt, PageBuilderContext $builderCtxt, RequestArrayAdapter $requestParams, ArrayAdapter $buildParams) {
        //1. ЗАГОЛОВОК
        $builderCtxt->setTitle($this->basicPage->getTitle());


        //2. JAVASCRIPT
        $jsParams['postId'] = $ctxt->getPostId();
        $jsParams['rubricId'] = $ctxt->getRubricId();
        $jsParams['postType'] = $ctxt->getPostType();

        $jsParams['isPostsListPage'] = $ctxt->isPostsListPage();
        $jsParams['isRubricPage'] = $ctxt->isRubricPage();
        $jsParams['isPostPage'] = $ctxt->isPostPage();

        //Разборы, пройденные пользователем
        $passed = TrainManager::inst()->getUserPassedLessons();
        $jsParams['passedLessons'] = empty($passed) ? null : $passed;

        //Структура проекта
        $jsParams['structure'] = NavigationManager::inst()->getStructure();
        $builderCtxt->setJsParams($jsParams);

        //Параметры, зависимые от страницы
        $builderCtxt->setJsParams($this->basicPage->getJsParams());


        //3. SMARTY RESOURCES
        $builderCtxt->setSmartyParams4Resources($this->basicPage->getSmartyParams4Resources());


        //4. GET SMARTY PARAMS FOR TPL
        $smartyParams['host'] = ServerArrayAdapter::HTTP_HOST();
        $smartyParams['content'] = BasicPagesManager::inst()->getResourcesLinks($this->basicPage->getIdent(), ContentHelper::getContent($this->basicPage));

        return $smartyParams;
    }

    public function getProfiler() {
        return PsProfiler::inst('BasicPageBuilder');
    }

}

?>