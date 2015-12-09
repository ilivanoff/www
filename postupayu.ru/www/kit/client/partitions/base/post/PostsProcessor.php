<?php

abstract class PostsProcessor extends CommentsProcessor implements PagePreloadListener, NewsProvider {

    /** @var PostsDataLoader */
    private $POSTS;

    /** @var PostFoldedResources */
    private $POST_FOLDING;

    protected function __construct() {
        parent::__construct();
        $this->POSTS = new PostsDataLoader($this);
        $this->POST_FOLDING = new PostFoldedResources($this);
    }

    public abstract function postsTitle();

    public abstract function newsTitle();

    protected abstract function postTitleImpl();

    public function postTitle($post = null, $inflect = 0) {
        $post = $post ? $this->extractPost($post, true) : null;
        return InflectsManager::inst()->getInflection($this->postTitleImpl(), $inflect) . ($post ? ': ' . $post->getName() : '');
    }

    public function postUrl($post, $sub = null, array $urlParams = array()) {
        $post = $this->extractPost($post, true);
        $baseUrl = $this->getPostPage()->getPath();
        $urlParams[GET_PARAM_POST_ID] = $post->getId();
        return PsUrl::addParams($baseUrl, $urlParams, $sub);
    }

    public function postHref($post, $content = null, $sub = null, $attrs = null, $blank = false) {
        $post = $this->extractPost($post, false);
        if (!$post) {
            /* Если поста нет, то ругаться не будем - нужно для предпросмотра поста */
            return PsHtml::spanErr($this->postTitle() . ' не определён');
        }

        $attrs = to_array($attrs);

        $attrs['href'] = $this->postUrl($post, $sub);
        if (!$sub) {
            $attrs['title'] = $this->postTitle($post);
        }
        $content = $content ? $content : $post->getName();

        return PsHtml::a($attrs, $content, $blank);
    }

    /**
     * Метод проверяет, относится ли данный процессор к одному из постов переданного типа
     */
    public function isIt($type) {
        return in_array($this->getPostType(), to_array($type));
    }

    /*
     *
     * ======================================
     * ===== МЕТОДЫ ДЛЯ ПЕРЕОПРЕДЕЛЕНИЯ =====
     * ======================================
     *
     */
    /*
     * Страница, на которой показаны все посты.
     */

    /** @return WebPage */
    public abstract function getPostsListPage()

    ;

    /*
     * Страница для показа конкретного поста.
     */

    /** @return WebPage */
    public abstract function getPostPage()

    ;

    /*
     * Увеличивает кол-во просмотров поста.
     */

    protected function increasePostRevCount(Post $post) {
        $this->dbBean()->increasePostRevCount($post->getId());
    }

    /** @return Post */
    public function getCurrentPost() {
        $post = null;
        if (PageContext::inst()->getPage()->isIt($this->getPostPage())) {
            if (ALLOW_DIRECT_BY_IDENT) {
                $post = $this->extractPost(RequestArrayAdapter::inst()->str(GET_PARAM_POST_ID));
            } else {
                $post = $this->getPost(RequestArrayAdapter::inst()->int(GET_PARAM_POST_ID));
            }
        }
        return $post ? $this->getPostContent($post->getId()) : null;
    }

    //Посты отсортированы от самого позднего к самому раннему: 3, 2, 1
    public function getPosts() {
        return $this->POSTS->getEntitys();
    }

    public function hasPostWithId($id) {
        return $this->POSTS->hasWithId($id);
    }

    public function hasPostWithIdent($ident) {
        return $this->POSTS->hasWithIdent($ident);
    }

    public function getPostsCount() {
        return $this->POSTS->getCount();
    }

    public function hasPosts() {
        return $this->POSTS->hasItems();
    }

    public function assertPostExists($id) {
        return $this->POSTS->assertExistsWithId($id);
    }

    /** @return Post */
    public function getPost($id, $ensure = false) {
        return $this->POSTS->getById($id, $ensure);
    }

    public function getPostIdByIdent($ident) {
        return $this->POSTS->getIdByIdent($ident);
    }

    /** @return Post */
    public function getPostByIdent($ident, $ensure = false) {
        return $this->POSTS->getByIdent($ident, $ensure);
    }

    /** @return Post */
    private function extractPost($ident, $ensure = false) {
        if ($ident instanceof Post) {
            return $this->getPost($ident->getId(), $ensure);
        }
        if ($ident instanceof AbstractPost) {
            return $this->getPostByIdent($ident->getIdent(), $ensure);
        }
        return $this->POSTS->extractEntity($ident, $ensure);
    }

    /**
     * Загружает посты, опубликованные до переданного
     */
    public function getPostsBeforPost($postId, $limit) {
        return $this->dbBean()->getPostsBeforPost($postId, $limit);
    }

    /**
     * Методы возвращают следующий (по дате) и предыдущий посты.
     */
    public function getNextPost($postId, $round = true) {
        $posts = $this->getPosts();

        $current = reset($posts);

        /* @var $current AbstractPost */
        while ($current) {
            if ($current->getId() == $postId) {
                $val = prev($posts);
                return $val ? $val : ($round ? end($posts) : null);
            }
            $current = next($posts);
        }

        return null;
    }

    public function getPrevPost($postId, $round = true) {
        $posts = $this->getPosts();

        $current = reset($posts);

        /* @var $current AbstractPost */
        while ($current) {
            if ($current->getId() == $postId) {
                $val = next($posts);
                return $val ? $val : ($round ? reset($posts) : null);
            }
            $current = next($posts);
        }

        return null;
    }

    /*
     *
     *
     * ==========================
     * Провайдеры данных о постах
     * ==========================
     *
     *
     * После загрузки полной информации о посте для него также создаётся провайдер
     * информации, который определяет по типу поста какую информацию о нём отображать.
     * Этот провайдер регистрируется в системе и далее доступен для вызовов.
     *
     *
     */

    /** @return Post */
    private function getPostContent($id) {
        return $this->POSTS->getContentById($id);
    }

    /** @return PostContentProvider */
    public function getPostContentProvider($id) {
        return $this->POSTS->hasWithId($id) ? ContentProviderFactory::getContentProvider($this->POSTS->getContentById($id)) : null;
    }

    /** @return PostContentProvider */
    public function getPostContentProviderByIdent($ident) {
        return $this->POSTS->hasWithIdent($ident) ? ContentProviderFactory::getContentProvider($this->POSTS->getContentByIdent($ident)) : null;
    }

    /** @return PostContentProvider */

    /** @deprecated */
    public function getCurrentPostContentProvider() {
        return $this->getPostContentProvider($this->getCurrentPost()->getId());
    }

    public function preloadPostsContentByIds($ids) {
        $this->POSTS->getContentsByIds($ids);
    }

    public function preloadAllPostsContent() {
        $this->POSTS->preloadAllContents();
    }

    /*
     * --== PAGING CONTROLLER ==--
     */

    //Метод возвращает полное кол-во постов на странице до постраничной разбивки
    protected function getCurrentPagePostsCountWithoutPaging() {
        return PageContext::inst()->isIt($this->getPostsListPage()) ? $this->getPostsCount() : null;
    }

    //Метод возвращает базовый урл (для присоединения параметра с пейджингом)
    protected function getCurrentPageBaseUrl() {
        return PageContext::inst()->isIt($this->getPostsListPage()) ? $this->getPostsListPage()->getPath() : null;
    }

    //Возвращает коды постов, которые должны быть отображены на странице (с учётом разбивки)
    protected function getCurrentPagePostsIds($pagingNum) {
        return PageContext::inst()->isIt($this->getPostsListPage()) ? $this->dbBean()->getPagingPostsIds($pagingNum) : null;
    }

    //Возвращает кол-во страниц (пейджингов), необходимых для размещения всех постов.
    private function getPagingsCnt() {
        $postsCount = $this->getCurrentPagePostsCountWithoutPaging();
        return is_numeric($postsCount) ? ceil($postsCount / POSTS_IN_ONE_PAGING) : null;
    }

    //Возвращает номер текущего пейджинга. Первым считается пейджинг с номером 1.
    private function getPagingNum() {
        $pagingsCnt = $this->getPagingsCnt();
        $pagingRq = RequestArrayAdapter::inst()->int(GET_PARAM_PAGE_NUM, 1);
        return is_numeric($pagingsCnt) ? min(array(max(array(1, $pagingRq)), $pagingsCnt)) : null;
    }

    /**
     * Метод возвращает контроллер для постраничного переключения постов.
     * Сам он будет построен силами javascript, наша задача только добавить <div> со всем данными.
     */
    public function getPagingController() {
        $pagingsCnt = $this->getPagingsCnt();
        $pagingNum = $this->getPagingNum();
        $baseUrl = $this->getCurrentPageBaseUrl();

        if ($pagingsCnt && ($pagingsCnt > 1) && $pagingNum && $baseUrl) {
            return PsHtml::div(array(
                        'class' => 'ps-switcher',
                        'data' => array('max' => $pagingsCnt, 'cur' => $pagingNum, 'url' => $baseUrl)
                    ));
        }

        return '';
    }

    private $PAGE_POSTS_IDS;

    public function getPagePostsIds() {
        if (!isset($this->PAGE_POSTS_IDS)) {
            $this->PAGE_POSTS_IDS = to_array($this->getCurrentPagePostsIds($this->getPagingNum()));
            $this->preloadPostsContentByIds($this->PAGE_POSTS_IDS);
        }
        return $this->PAGE_POSTS_IDS;
    }

    /*
     * --== РЕАЛИЗАЦИЯ PagePreloadListener ==--
     * 
     *  Прослушивание события открытия страницы
     */

    public function onPagePreload(WebPage $page) {
        switch ($page->getCode()) {
            case $this->getPostsListPage()->getCode():
                $this->onPostsListShow();
                break;
            case $this->getPostPage()->getCode():
                $this->onPostShow();
                break;
        }
    }

    /**
     * Метод вызывается до показа списка постов.
     */
    protected function onPostsListShow() {
        
    }

    /**
     * Метод вызывается до показа поста.
     */
    protected function onPostShow() {
        if (PostsWatcher::registerPostWatch($this->getCurrentPost())) {
            $this->increasePostRevCount($this->getCurrentPost());
            $this->getCurrentPostContentProvider()->getPost()->incRevCount();
        }
    }

    /*
     * Ресурсы поста
     */

    /** @return FoldedResources */
    public function getFolding() {
        return $this->POST_FOLDING;
    }

    /**
     * Метод возвращает идентификатор очередного поста, который может быть создан
     */
    public function getNextFoldingIdent() {
        return $this->getPostType() . 'new';
    }

    /**
     * Метод устанавливает дополнительные параметры для виртуальных постов
     */
    protected function addVirtualPostParams($ident, array $cols4replace) {
        $cols4replace['name'] = array_get_value('name', $cols4replace, $this->postTitle() . ' ' . $ident);
        $cols4replace['dt_publication'] = array_get_value('dt_publication', $cols4replace, time());
        return $cols4replace;
    }

    /**
     * Метод возвращает "виртуальный пост" - такой пост, каким он мог бы быть, если бы существовал в базе.
     * Если пост с таким идентфикатором в базе есть, то он и будет возвращён.
     * 
     * @param type $ident - идентификатор желаемого поста
     * @param array $cols4replace - параметры, которые будут заменены в случае, если пост будет виртуальным
     */
    public function getVirtualPost($ident, array $cols4replace = array()) {
        return $this->dbBean()->getVirtualPost($ident, $this->addVirtualPostParams($ident, $cols4replace));
    }

    /*
     * РАБОТА С ШАБЛОНАМИ
     */

    public function getAccessibleTemplates($includePattern = false) {
        return $this->POST_FOLDING->getAccesibleResourcesDi(FoldedResources::RTYPE_TPL, $includePattern);
    }

    /** @return AbstractPost */
    public function getAbstractPost($ident) {
        $this->POST_FOLDING->assertExistsEntity($ident);
        return new AbstractPost($this->getPostType(), $ident);
    }

    /*
     * ОБЛОЖКИ
     */

    /* Размер обложки по умолчанию */

    public abstract function coverDims();

    /** @return DirItem */
    public function getCoverDi($ident, $dim) {
        return $this->POST_FOLDING->getCover($ident, $dim);
    }

    /** @return DirItem */
    public function getCoverDi4Show($ident) {
        return $this->POST_FOLDING->getCover($ident);
    }

    /*
     * ФОРМУЛЫ
     */

    public function getPostFormules($ident) {
        return TexImager::inst()->extractTexImages($this->POST_FOLDING->getTplDi($ident)->getFileContents());
    }

    /**
     * 
     * НОВОСТИ
     * 
     */
    public function getNewsEvents($postId, $limit) {
        return $this->getPostsBeforPost($postId, $limit);
    }

    public function getNewsEventType() {
        return $this->getPostType();
    }

    public function preloadNewsEvents(array $postsIds) {
        $this->preloadPostsContentByIds($postsIds);
    }

    public function getNewsEventPresentation(NewsEventInterface $event) {
        $cp = $this->getPostContentProvider($event->getNewsEventUnique());
        return PSSmarty::template('post/post_news_item.tpl', array('cp' => $cp))->fetch();
    }

}

?>