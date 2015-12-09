<?php

class CommentsPostItem {

    private $post;

    public function __construct(Post $post) {
        $this->post = $post;
    }

    public function getPost() {
        return $this->post;
    }

    private $items = array();

    public function add(CommentsLineItem $item) {
        $this->items[] = $item;
    }

    public function getItems() {
        return $this->items;
    }

}

?>
