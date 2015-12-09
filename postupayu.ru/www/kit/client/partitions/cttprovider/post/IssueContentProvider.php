<?php

class IssueContentProvider extends PostContentProviderTpl {

    public function getPostContentShowcase($cached = true) {
        return $this->getPostContent($cached);
    }

}

?>
