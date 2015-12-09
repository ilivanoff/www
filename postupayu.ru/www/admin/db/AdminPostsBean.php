<?php

class AdminPostsBean extends BaseBean {

    public function getAllPosts(PostsBean $bean) {
        $postTable = $bean->getPostsTable();

        $hiddenArr = $this->getArray("SELECT *
  FROM $postTable p
 WHERE p.id_post NOT IN (SELECT id_post FROM v_$postTable)
  and (p.b_show=0 or p.dt_publication is null)
ORDER BY ifnull(p.dt_publication, id_post) DESC");

        $hidden = array();
        foreach ($hiddenArr as $data) {
            $hidden[] = new AdminPost($bean->getPostType(), $data);
        }

        $readyArr = $this->getArray("SELECT *
  FROM $postTable p
 WHERE p.id_post NOT IN (SELECT id_post FROM v_$postTable)
  and p.dt_publication is not null and p.b_show=1
ORDER BY dt_publication DESC, id_post DESC");

        $ready = array();
        foreach ($readyArr as $data) {
            $ready[] = new AdminPost($bean->getPostType(), $data);
        }

        $shownArr = $this->getArray("SELECT * FROM v_$postTable");

        $shown = array();
        foreach ($shownArr as $data) {
            $shown[] = new AdminPost($bean->getPostType(), $data);
        }

        return array('hidden' => $hidden, 'ready' => $ready, 'shown' => $shown);
    }

    public function getAllRubrics(RubricsBean $bean) {
        $rubricTable = $bean->getRubricsTable();

        $rubricsArr = $this->getArray("SELECT * FROM $rubricTable r ORDER BY r.name");

        $rubrics = array();
        foreach ($rubricsArr as $data) {
            $rubric = new Rubric($bean->getPostType(), $data);
            $rubrics[$rubric->getId()] = $rubric;
        }

        return $rubrics;
    }

    public function updateState(PostsBean $bean, $id, $dateUTS, $show) {
        $postTable = $bean->getPostsTable();
        $this->update("update $postTable set dt_publication=?, b_show=? where id_post=?", array(
            $dateUTS,
            $show ? 1 : 0,
            $id
        ));
    }

    public function registerPost(PostsBean $bean, $ident, $name, $rubId) {
        $hasRub = $bean instanceof RubricsBean;
        $table = $bean->getPostsTable();

        $postId;
        $post = $this->getPostByIdent($bean, $ident);
        if ($post == null) {
            if ($hasRub) {
                $postId = $this->insert("INSERT INTO $table
(id_rubric, name, ident, b_show, dt_publication, rev_count, content, content_showcase, b_tpl) 
VALUES (?, ?, ?, 0, unix_timestamp(), 0, null, null, 1)", array($rubId, $name, $ident));
            } else {
                $postId = MagManager::ident2id($ident);
                $this->insert("INSERT INTO $table
(id_post, name, ident, b_show, rev_count) 
VALUES (?, ?, ?, 0, 0)", array($postId, $name, $ident));
            }
        } else {
            $postId = $post->getId();
        }

        $this->update("update $table set name=? where id_post=?", array(
            $name,
            $postId
        ));

        if ($hasRub) {
            $this->update("update $table set id_rubric=? where id_post=?", array(
                $rubId,
                $postId
            ));
        }
    }

    /** @return Post */
    public function getPostByIdent(PostsBean $bean, $ident) {
        $postTable = $bean->getPostsTable();
        $postType = $bean->getPostType();
        $dataArr = $this->getRec("SELECT * FROM $postTable p where p.ident=?", $ident);
        return $dataArr == null ? null : new AdminPost($postType, $dataArr);
    }

    /*
     * СИНГЛТОН
     */

    /** @return AdminPostsBean */
    public static function inst() {
        return parent::inst();
    }

}

?>
