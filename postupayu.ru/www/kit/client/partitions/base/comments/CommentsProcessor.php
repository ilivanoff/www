<?php

abstract class CommentsProcessor extends AbstractSingleton {

    /** @var PsLoggerInterface */
    protected $LOGGER;

    /** @var CommentsDiscussionController */
    private $DISCUSSION;

    protected function __construct() {
        $this->LOGGER = PsLogger::inst(get_called_class());
        $this->DISCUSSION = new CommentsDiscussionController($this);
    }

    /** @return CommentsDiscussionController */
    public final function getDiscussionController() {
        return $this->DISCUSSION;
    }

    public final function buildDiscussion($postId, $limited) {
        $limited = $limited && !GetArrayAdapter::inst()->str(GET_PARAM_GOTO_MSG);
        return $this->DISCUSSION->buildDiscussion(true, $postId, $limited);
    }

    /** @return RubricsBean */
    public abstract function dbBean()

    ;

    public abstract function assertPostExists($id);

    /*
     * Возвращает тип постов, к которому отнесены комментарии.
     * is-выпуск журнала
     * bp-блог
     * tr-разбор
     * un-юниты
     */

    public abstract function getPostType()

    ;

    public final function buildSimpleDiscussion(Comment $comment) {
        return $this->DISCUSSION->buildDiscussionSimple($comment);
    }

    public function getUserCommentsAnswersCnt($userId) {
        return $this->DISCUSSION->getUserUnknownMsgsCnt($userId);
    }

    public function getUserCommentedMessages($userId) {
        return $this->DISCUSSION->getUserUnknownMsgs($userId);
    }

    public function markUserMessageAsReaded($commentId, $userId) {
        $this->DISCUSSION->markMsgChildsAsKnown($commentId, $userId);
    }

    /**
     * Получает формулы TeX для поста
     */
    public function getCommentsFormules($postId = null) {
        $formules = array();
        foreach ($this->DISCUSSION->getMsgsContentWithTex($postId) as $comment) {
            $formules = array_merge($formules, TexImager::inst()->extractTexImages($comment));
        }
        return $formules;
    }

}

?>