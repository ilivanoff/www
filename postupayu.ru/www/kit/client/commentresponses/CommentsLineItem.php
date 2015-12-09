<?php

class CommentsLineItem {

    private $comment;

    public function __construct(Comment $comment) {
        $this->comment = $comment;
    }

    public function getId() {
        return IdHelper::ident($this->comment);
    }

    public function discHtml() {
        $pp = Handlers::getInstance()->getCommentsProcessorByPostType($this->comment->getPostType());
        return $pp->buildSimpleDiscussion($this->comment);
    }

    public function commentUrl() {
        $pp = Handlers::getInstance()->getPostsProcessorByPostType($this->comment->getPostType());
        $msgUnique = $this->comment->getUnique();
        return $pp->postUrl($this->comment->getPostId(), $msgUnique, array(GET_PARAM_GOTO_MSG => $msgUnique));
    }

}

?>
