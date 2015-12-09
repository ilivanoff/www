<?php

class IP_usercomments extends BaseOfficePage implements NumerableOfficePage {

    public function getTitle() {
        return 'Новые комментарии';
    }

    public function getNumericState() {
        return CommentsNotifyManager::getInstance()->getUserPostCommentsCnt(AuthManager::getUserId());
    }

    public function processRequest(ArrayAdapter $params) {
        $trees = CommentsNotifyManager::getInstance()->getUserPostCommentsInfo(AuthManager::getUserId());
        return new IdentPageFilling(array('trees' => $trees));
    }

}

?>