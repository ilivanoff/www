<?php

abstract class PostContentProvider {

    /** @var Post */
    protected $postContent;

    /** @var PsLoggerInterface */
    protected $LOGGER;

    /** @var PostsProcessor */
    protected $pp;

    public function __construct(Post $postContent) {
        $this->postContent = $postContent;
        $this->LOGGER = PsLogger::inst(get_called_class());
        $this->pp = Handlers::getInstance()->getPostsProcessorByPostType($postContent->getPostType());
    }

    /** @return Post */
    public function getPost() {
        return $this->postContent;
    }

    public function getPostPopupVariant() {
        $postType = $this->postContent->getPostType();
        return PSSmarty::template("$postType/post_popup.tpl", array('postCP' => $this))->fetch();
    }

    public function getPostPrintVariant() {
        $postType = $this->postContent->getPostType();
        return PSSmarty::template("$postType/post_print.tpl", array('postCP' => $this))->fetch();
    }

    /** @return PostsProcessor */
    public function pp() {
        return $this->pp;
    }

    /** @return FetchParams */
    public abstract function getPostParams($cached = true);

    /** @return FetchParams */
    public abstract function getPostContent($cached = true);

    /** @return FetchParams */
    public abstract function getPostContentShowcase($cached = true);
}

?>