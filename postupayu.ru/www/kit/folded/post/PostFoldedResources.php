<?php

class PostFoldedResources extends FoldedResources implements StorableFolding, DatabasedFolding, ImagedFolding {

    private $pp;
    protected $RESOURCE_TYPES_ALLOWED = array(self::RTYPE_JS, self::RTYPE_CSS, self::RTYPE_TPL);

    public function __construct(PostsProcessor $pp) {
        $this->pp = $pp;
        parent::__construct();
    }

    public function getEntityName() {
        return $this->pp->postTitle();
    }

    public function getFoldingType() {
        return 'post';
    }

    public function getFoldingSubType() {
        return $this->pp->getPostType();
    }

    public function getFoldingGroup() {
        return next_level_dir('posts', $this->pp->getPostType());
    }

    /**
     * Возвращает менеджера постов для данного фолдинга
     * 
     * @return PostsProcessor
     */
    public function pp() {
        return $this->pp;
    }

    function defaultDim() {
        return $this->pp->coverDims();
    }

    protected function isIncludeToList($ident, $list) {
        return true;
    }

    /*
     * КАСТОМНЫЕ МЕТОДЫ
     */

    protected function onEntityChangedImpl($ident) {
        
    }

    public function getFoldedEntityPreview($ident) {
        $type = $this->pp->getPostType();
        $postCP = $this->pp->getPostContentProviderByIdent($ident);
        $hasInDb = is_object($postCP);
        $post = null;
        if ($hasInDb) {
            $post = $postCP->getPost();
        } else {
            //Сделаем его форсированно загружаемым из шаблона
            $virtualPostParams['b_tpl'] = 1;
            $post = $this->pp->getVirtualPost($ident, $virtualPostParams);
            $postCP = ContentProviderFactory::getContentProvider($post);
        }

        //Накачиваем страницу

        $PARAMS = array('type' => $type, 'full' => '', 'short' => '', 'error' => '');
        try {
            $PARAMS['full'] = $postCP->getPostContent()->getContent();
            $PARAMS['short'] = $postCP->getPostContentShowcase()->getContent();
        } catch (Exception $ex) {
            $PARAMS['error'] = ExceptionHandler::getHtml($ex);
        }

        $info = $hasInDb ? $this->pp->postHref($post, null, null, null, true) : PsHtml::gray($post->isVirtual() ? 'Пост не зарегистрирован в базе' : 'Пост зарегистрирован в базе, но не виден пользователю');
        $content = PSSmarty::template('post/folded_prev.tpl', $PARAMS)->fetch();

        return array(
            'info' => $info,
            'content' => $content
        );
    }

    public function getNextEntityIdent() {
        return $this->pp->getNextFoldingIdent();
    }

    function foldingTable() {
        return $this->pp->dbBean()->getPostsView() . '.ident';
    }

    function dbRec4Entity($ident) {
        return $this->pp->getVirtualPost($ident)->getDbRow();
    }

    protected function getFoldedContext() {
        return PostFetchingContext::getInstance();
    }

}

?>