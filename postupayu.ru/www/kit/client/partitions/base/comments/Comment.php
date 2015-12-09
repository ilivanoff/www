<?php

class Comment extends DiscussionMsg {

    public function getPostType() {
        return $this->SETTINGS->getSubgroup();
    }

    public function getPostId() {
        return $this->getThreadId();
    }

}

?>