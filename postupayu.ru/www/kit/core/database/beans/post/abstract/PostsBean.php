<?php

abstract class PostsBean extends CommentsBean {

    /** @return Post */
    protected function fetchPost(array $postData) {
        return new Post($this->getPostType(), $postData);
    }

    private function loadPosts($what, $where = null, $order = null, $limit = null) {
        return $this->getObjects(Query::select($what, $this->postsView . ' p', $where, null, $order, $limit), null, Post::getClass(), 'id_post', array($this->getPostType()));
    }

    public function getPosts() {
        return $this->loadPosts(array('id_post, name, ident, dt_publication', $this->rubricsTable ? 'id_rubric' : ''));
    }

    public function getPostsContent(array $ids, $loadAll = false) {
        //what
        $what[] = 'p.*';
        $what[] = "(select count(1) from $this->commentsTable where b_deleted=0 and id_post=p.id_post) as comments_count";
        if ($loadAll) {
            return $this->loadPosts($what);
        } else {
            $result = array();
            /** @var QueryParamAssoc */
            foreach (Query::assocParamsIn('id_post', $ids) as $param) {
                foreach ($this->loadPosts($what, $param) as $postId => $post) {
                    $result[$postId] = $post;
                }
            }
            return $result;
        }
    }

    public function getPostsBeforPost($postId, $limit) {
        return $this->loadPosts(
                        //WHAT
                        'id_post, dt_publication',
                        //WHERE
                        is_inumeric($postId) ?
                                Query::assocParam('dt_publication', "(select i.dt_publication from $this->postsView i where i.id_post=?)", false, '<', array($postId)) :
                                null,
                        //ORDER
                        array('dt_publication desc', 'id_post desc'),
                        //LIMIT
                        $limit
        );
    }

    public function getPagingPostsIds($pagingNumber = null, $rubricId = null, $postsOnPage = POSTS_IN_ONE_PAGING) {
        $limit = is_inumeric($pagingNumber) ? ($pagingNumber - 1) * $postsOnPage : -1;
        $limit = $limit < 0 ? null : array($limit, $postsOnPage);

        return $this->getIds(Query::select(
                                //WHAT
                                'id_post as id',
                                //FROM
                                $this->postsView,
                                //WHERE
                                is_inumeric($rubricId) ? array('id_rubric' => $rubricId) : null,
                                //GROUP
                                null,
                                //ORDER (уже отсортировано во вьюхе)
                                null,
                                //LIMIT
                                $limit));
    }

    /**
     * Увеличение счётчика просмотров поста
     */
    public function increasePostRevCount($postId) {
        $this->update("update $this->postsTable set rev_count = rev_count+1 where id_post = ?", $postId);
    }

    /**
     * Метод загружает виртуальный пост. Если он есть в базе, то будет возвращён пост из базы, если нет, то будет возвращён
     * виртуальный пост.
     * 
     * @param string $ident - идентификатор поста
     * @param array $cols4replace - столбцы, которые можно заменить своими значениями в случае, если строка не будет загружена из БД
     */
    public function getVirtualPost($ident, array $cols4replace = array()) {
        check_condition($ident, 'Не передан идентификатор для загрузки виртуального поста');

        //Проверим, возможно запись уже есть в таблице. Если так - вернём её.
        $row = $this->getRec("select * from $this->postsTable where ident=?", $ident);
        $virtual = !is_array($row);
        if ($virtual) {
            $cols4replace['ident'] = $ident;
            $cols4replace['id_post'] = TEST_ENTITY_ID;
            $row = $this->getEmptyRec($this->postsTable, $cols4replace);
        }

        return $this->fetchPost($row);
    }

}

?>