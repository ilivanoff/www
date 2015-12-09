<?php

class PostContentProviderDB extends PostContentProvider {

    public function getPostContent($cached = true) {
        return new FetchParams(array(FetchParams::PARAM_CONTENT => $this->postContent->getContent()));
    }

    public function getPostContentShowcase($cached = true) {
        return new FetchParams(array(FetchParams::PARAM_CONTENT => $this->postContent->getShowcase()));
    }

    public function getPostParams($cached = true) {
        return new FetchParams(array());
    }

}

?>