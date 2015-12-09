<?php

class PostsDataLoader extends IdIdentDataLoader {

    private $pp;

    public function __construct(PostsProcessor $pp) {
        $this->pp = $pp;
    }

    protected function entityTitle() {
        return $this->pp->postTitle();
    }

    protected function loadEntitysLiteDB() {
        return $this->pp->dbBean()->getPosts();
    }

    protected function loadEntitysFullDB(array $ids, $loadAll) {
        return $this->pp->dbBean()->getPostsContent($ids, $loadAll);
    }

}

?>
