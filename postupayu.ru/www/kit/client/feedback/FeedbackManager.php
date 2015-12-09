<?php

class FeedbackManager extends DiscussionController {

    /**
     * Метод сохраняет сообщение анонимного пользователя
     */
    public function saveAnonimousFeedback($name, $contacts, $theme, $text) {
        FeedBean::inst()->saveAnonimousFeedback($name, $contacts, $theme, $text);
    }

    /**
     * Получает количество новых ответов на сообщения пользователя в рамках обратной связи
     */
    public function getUserUnreadedFeedbacksCount() {
        return $this->getUserUnknownMsgsCnt(AuthManager::getUserId(), AuthManager::getUserId());
    }

    /**
     * From DiscussionController
     */
    protected function assertCanSaveDiscussionMsg(PsUser $author, $parent = null, $threadId = null) {
        //В ленту пишет сам пользователь или мы авторизованы под администратором или от имени дефолтного администратора
        $author->isIt($threadId) || $author->isIt(PsUser::defaultAdmin()) || AuthManager::isAuthorizedAsAdmin();
    }

    protected function assertValidDiscussionEntityId($threadId) {
        //Проверим существование пользователя
        PsUser::inst($threadId, true);
    }

    protected function getIdUserTo4Root(PsUser $author, $threadId) {
        //Если пользователь сам пишетв ленту, то это сообщение - админу, в противном случае все сообщения идут самому пользователю
        return $author->isIt($threadId) ? DEFAULT_ADMIN_USER : $threadId;
    }

    protected function discusionSettings() {
        return new DiscussionSettings('feed', null, 'feedback_user', 'id_feedback', 'DiscussionMsg', 'id_owner', 'theme', true, false);
    }

    /*
     * html - хелперы
     */

    public function writeToUsHref($content = 'Напишите нам', $blank = false, $http = false, $classes = 'write_to_us') {
        return WebPage::inst(BASE_PAGE_FEEDBACK)->getHref($content, $blank, $classes, $http, null, 'feed');
    }

    /*
     * СИНГЛТОН
     */

    private static $inst;

    /** @return FeedbackManager */
    public static function inst() {
        return self::$inst ? self::$inst : new FeedbackManager();
    }

}

?>