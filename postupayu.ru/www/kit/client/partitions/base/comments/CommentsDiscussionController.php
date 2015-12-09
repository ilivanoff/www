<?php

/**
 * Класс содержит в себе всю логику по работе с деревьямикомментариев к посту
 *
 * @author azazello
 */
class CommentsDiscussionController extends DiscussionController {

    /** @var CommentsProcessor */
    private $CP;

    function __construct(CommentsProcessor $commentsProcessor) {
        $this->CP = $commentsProcessor;
        parent::__construct();
    }

    protected function assertCanSaveDiscussionMsg(PsUser $user, $parent = null, $threadId = null) {
        //Если пост существует - коммент может быть добавлен
    }

    protected function assertValidDiscussionEntityId($threadId) {
        $this->CP->assertPostExists($threadId);
    }

    protected function discusionSettings() {
        return new DiscussionSettings('post', $this->CP->getPostType(), $this->CP->dbBean()->getCommentsTable(), 'id_comment', 'Comment', 'id_post');
    }

    protected function getIdUserTo4Root(PsUser $user, $threadId) {
        return null;
    }

}

?>