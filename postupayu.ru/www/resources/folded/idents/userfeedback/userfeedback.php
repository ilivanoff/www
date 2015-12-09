<?php

class IP_userfeedback extends BaseOfficePage implements NumerableOfficePage {

    public function getTitle() {
        return 'Обратная связь';
    }

    public function getNumericState() {
        return FeedbackManager::inst()->getUserUnreadedFeedbacksCount();
    }

    protected function processRequest(ArrayAdapter $params) {
        $smarty['discussion'] = FeedbackManager::inst()->buildDiscussion(false, AuthManager::getUserId(), true);
        return new IdentPageFilling($smarty);
    }

}

?>