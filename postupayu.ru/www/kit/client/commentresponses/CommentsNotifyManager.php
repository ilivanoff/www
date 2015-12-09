<?php

/**
 * Менеджер для работы с ответами на сообщения пользователя
 */
class CommentsNotifyManager extends AbstractSingleton {

    public function getUserPostCommentsInfo($userId) {
        $pps = Handlers::getInstance()->getPostsProcessors();

        $trees = array();
        /* @var $pp PostsProcessor */
        foreach ($pps as $pp) {
            $tree = $pp->getUserCommentedMessages($userId);
            /** @var Comment */
            foreach ($tree as $commented) {
                $postId = $commented->getPostId();
                $postType = $commented->getPostType();
                //тип_поста->код_коста->корневые_комментарии
                if (!array_key_exists($postType, $trees)) {
                    $trees[$postType] = array();
                }
                if (!array_key_exists($postId, $trees[$postType])) {
                    $trees[$postType][$postId] = new CommentsPostItem($pp->getPost($postId));
                }
                $trees[$postType][$postId]->add(new CommentsLineItem($commented));
            }
        }
        return $trees;
    }

    public function getUserPostCommentsCnt($userId) {
        $total = 0;
        /* @var $cp CommentsProcessor */
        foreach (Handlers::getInstance()->getCommentProcessors() as $cp) {
            $cnt = $cp->getUserCommentsAnswersCnt($userId);
            $total += $cnt;
        }
        return $total;
    }

    /** @return CommentsNotifyManager */
    public static function getInstance() {
        return parent::inst();
    }

}

?>
