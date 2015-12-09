<?php

abstract class RubricsProcessor extends PostsProcessor {

    /** @var RubricsDataLoader */
    private $RUBRICS;

    /** @var RubricFoldedResources */
    private $RUBRIC_FOLDING;

    protected function __construct() {
        parent::__construct();
        $this->RUBRICS = new RubricsDataLoader($this);
        $this->RUBRIC_FOLDING = new RubricFoldedResources($this);
    }

    protected abstract function rubricTitleImpl();

    public function rubricTitle($rubric = null, $inflect = 0) {
        $rubric = $rubric ? $this->extractRubric($rubric) : null;
        return InflectsManager::inst()->getInflection($this->rubricTitleImpl(), $inflect) . ($rubric ? ': ' . $rubric->getName() : '');
    }

    public function rubricUrl($rubric) {
        $rubric = $this->extractRubric($rubric);
        return $this->getRubricPage()->getPath() . '?' . GET_PARAM_RUBRIC_ID . '=' . $rubric->getId();
    }

    public function rubricHref($rubric, $content = null, $classes = null) {
        $rubric = $this->extractRubric($rubric);
        $content = $content ? $content : $rubric->getName();

        $PARAMS['href'] = $this->rubricUrl($rubric);
        $PARAMS['class'] = $classes;
        $PARAMS['title'] = $this->rubricTitle($rubric);

        return PsHtml::a($PARAMS, $content);
    }

    /*
     * Страница, на которой показаты посты конкретной рубрики.
     */

    /** @return WebPage */
    public abstract function getRubricPage()

    ;

    /** @return FoldedResources */
    public function getRubricsFolding() {
        return $this->RUBRIC_FOLDING;
    }

    /*
     *
     * ======================================
     * =============== ДАННЫЕ ===============
     * ======================================
     *
     */

    /** @return Rubric */
    public function getCurrentRubric() {
        $curPage = PageContext::inst()->getPage();
        if ($curPage->isIt($this->getRubricPage())) {
            $rubric = null;
            if (ALLOW_DIRECT_BY_IDENT) {
                $rubric = $this->extractRubric(RequestArrayAdapter::inst()->str(GET_PARAM_RUBRIC_ID));
            } else {
                $rubric = $this->getRubric(RequestArrayAdapter::inst()->int(GET_PARAM_RUBRIC_ID));
            }
            return $rubric ? $this->getRubricContent($rubric->getId()) : null;
        }
        if ($curPage->isIt($this->getPostPage())) {
            return $this->getRubricContent($this->getCurrentPost()->getRubricId());
        }
        return null;
    }

    /*
     * Рубрики
     */

    public function getRubrics() {
        return $this->RUBRICS->getEntitys();
    }

    /** @return Rubric */
    public function getRubric($id, $ensure = false) {
        return $this->RUBRICS->getById($id, $ensure);
    }

    /** @return Rubric */
    public function getRubricByIdent($ident, $ensure = false) {
        return $this->RUBRICS->getByIdent($ident, $ensure);
    }

    public function hasRubrics() {
        return $this->RUBRICS->hasItems();
    }

    public function isRubricExists($id) {
        return $this->RUBRICS->hasWithId($id);
    }

    public function assertRubricExists($id) {
        $this->RUBRICS->assertExistsWithId($id);
    }

    /** @return Rubric */
    private function extractRubric($ident, $ensure = false) {
        if ($ident instanceof Rubric) {
            return $this->getRubric($ident->getId(), $ensure);
        }
        if ($ident instanceof AbstractRubric) {
            return $this->getRubricByIdent($ident->getIdent(), $ensure);
        }
        return $this->RUBRICS->extractEntity($ident, $ensure);
    }

    /** @return Rubric */
    private function getRubricContent($id) {
        return $this->RUBRICS->getContentById($id);
    }

    public function preloadAllRubricsContent() {
        $this->RUBRICS->preloadAllContents();
    }

    /*
     * Привязка постов в рубрики
     * [rubric_id][post_id]
     */

    private $POSTS_TO_RUBRIC;

    public function getPostToRubric() {
        if (!isset($this->POSTS_TO_RUBRIC)) {
            $this->POSTS_TO_RUBRIC = array();
            /* @var $post Post */
            foreach ($this->getPosts() as $post) {
                $this->POSTS_TO_RUBRIC[$post->getRubricId()][$post->getId()] = $post;
            }
        }
        return $this->POSTS_TO_RUBRIC;
    }

    /*
     *
     * ======================================
     * ===== МЕТОДЫ ДЛЯ ПЕРЕОПРЕДЕЛЕНИЯ =====
     * ======================================
     *
     */
    /*
     * Загрузка полной информации о рубрике из базы
     */

    /** @return RubricContentProvider */
    public function getRubricContentProvider($id) {
        return $this->RUBRICS->hasWithId($id) ? ContentProviderFactory::getContentProvider($this->RUBRICS->getContentById($id)) : null;
    }

    /** @return RubricContentProvider */
    public function getRubricContentProviderByIdent($ident) {
        return $this->RUBRICS->hasWithIdent($ident) ? ContentProviderFactory::getContentProvider($this->RUBRICS->getContentByIdent($ident)) : null;
    }

    /** @return RubricContentProvider */
    public function getCurrentRubricContentProvider() {
        return $this->getRubricContentProvider($this->getCurrentRubric()->getId());
    }

    /**
     * Возвращает кол-во постов.
     *
     * @param $rubricId - рубрика, кол-во постов в которой подсчитываем.
     * @return int кол-во постов
     */
    public function getPostsCount($rubricId = null) {
        if ($rubricId === null) {
            return parent::getPostsCount();
        } else {
            $this->assertRubricExists($rubricId);
            $p2r = $this->getPostToRubric();
            return count($p2r[$rubricId]);
        }
    }

    /*
     * --== PAGING CONTROLLER ==--
     * 
     * Работа с постраничной разбивкой. нам нужно знать:
     * 1. Сколько всего постов может быть отображено на странице (без учёта пейджинга)
     * 2. Какой base-url данной страны для перехода на пейджинг (например: rubric.php?rubric_id=1)
     * 3. Список постов для отображения на пейджинге
     */

    //@Override
    protected function getCurrentPagePostsCountWithoutPaging() {
        return PageContext::inst()->isIt($this->getRubricPage()) ? $this->getPostsCount($this->getCurrentRubric()->getId()) : parent::getCurrentPagePostsCountWithoutPaging();
    }

    //@Override
    protected function getCurrentPageBaseUrl() {
        return PageContext::inst()->isIt($this->getRubricPage()) ? $this->rubricUrl($this->getCurrentRubric()->getId()) : parent::getCurrentPageBaseUrl();
    }

    //@Override
    protected function getCurrentPagePostsIds($pagingNum) {
        return PageContext::inst()->isIt($this->getRubricPage()) ? $this->dbBean()->getPagingPostsIds($pagingNum, $this->getCurrentRubric()->getId()) : parent::getCurrentPagePostsIds($pagingNum);
    }

    /*
     * --== РЕАЛИЗАЦИЯ PagePreloadListener ==--
     * 
     *  Прослушивание события открытия страницы
     */

    public function onPagePreload(WebPage $page) {
        switch ($page->getCode()) {
            case $this->getRubricPage()->getCode():
                $this->onRubricShow();
                break;
            default:
                parent::onPagePreload($page);
                break;
        }
    }

    /*
     * Метод вызывается до показа рубрики.
     */

    protected function onRubricShow() {
        
    }

    /**
     * Метод устанавливает дополнительные параметры для виртуальных постов
     */
    protected function addVirtualRubricParams($ident, array $cols4replace) {
        $cols4replace['name'] = array_get_value('name', $cols4replace, $this->rubricTitle() . ' ' . $ident);
        return $cols4replace;
    }

    /**
     * Метод возвращает "виртуальную рубрику" - рубрику, какой она молаг бы быть, если бы существовала в базе.
     * Если рубрика с таким идентфикатором в базе есть, то она и будет возвращёна.
     * 
     * @param type $ident - идентификатор рубрики
     * @param array $cols4replace - параметры, которые будут заменены в случае, если рубрика не существует
     */
    public function getVirtualRubric($ident, array $cols4replace = array()) {
        return $this->dbBean()->getVirtualRubric($ident, $this->addVirtualRubricParams($ident, $cols4replace));
    }

}

?>