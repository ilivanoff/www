<?php

final class TestManager extends AbstractSingleton {

    /** @var PsLoggerInterface */
    private $LOGGER;

    /** @var TESTBean */
    private $BEAN;

    private function getPostsProcessor($postType) {
        if ($postType) {
            $proc = Handlers::getInstance()->getPostsProcessorByPostType($postType, false);
            return $proc ? array($postType => $proc) : array();
        } else {
            return Handlers::getInstance()->getPostsProcessors();
        }
    }

    private function getRubricsProcessor($postType) {
        if ($postType) {
            $proc = Handlers::getInstance()->getRubricsProcessorByPostType($postType, false);
            return $proc ? array($postType => $proc) : array();
        } else {
            return Handlers::getInstance()->getRubricsProcessors();
        }
    }

    private function getCommentsProcessor($postType) {
        if ($postType) {
            $proc = Handlers::getInstance()->getCommentsProcessorByPostType($postType, false);
            return $proc ? array($postType => $proc) : array();
        } else {
            return Handlers::getInstance()->getCommentProcessors();
        }
    }

    /*
     * 
     * 
     * 
     * 
     * ==========================
     *        ПОЛЬЗОВАТЕЛИ
     * ==========================
     * 
     * 
     * 
     * 
     */

    private $avatars;

    /** @return DirItem */
    private function getAvatarImg() {
        if (!isset($this->avatars)) {
            $this->avatars = array_values(DirManager::mmedia('avatars/test')->getDirContent(null, DirItemFilter::IMAGES));
            check_condition($this->avatars, 'No avatar images');
        }
        return $this->avatars[rand(0, count($this->avatars) - 1)];
    }

    /**
     * Генерация пользователей.
     * cnt - кол-во пользователей, которое будет сгенерировано
     */
    public final function genereteTestUsers($cnt = 10) {
        for ($index = 0; $index < $cnt; $index++) {
            $userId = $this->BEAN->createTestUser();
            $this->updateUserAvatars($userId);
        }
    }

    /**
     * Установка аватаров пользователя
     */
    public final function updateUserAvatars($userId = null) {
        $userIds = TESTBean::inst()->getUserIds($userId);
        foreach ($userIds as $userId) {
            $this->BEAN->unsetAvatarUploads($userId);
            $avatarDi = $this->getAvatarImg();
            $uploadedDi = AvatarUploader::inst()->makeUploadedFile($avatarDi, $userId);
            PsUser::inst($userId)->setAvatar($uploadedDi->getData('id'));
        }
    }

    /**
     * Удаление тестовых пользователей
     */
    public final function removeTestUsers() {
        $userIds = TESTBean::inst()->getUserIds();
        foreach ($userIds as $userId) {
            if ($this->BEAN->isTestUser($userId)) {
                $this->BEAN->unsetAvatarUploads($userId);
                $this->BEAN->removeTestUser($userId);
            }
        }
    }

    /**
     * Даёт очки пользователю
     */
    public final function givePoints2Users($userId = null, $cnt = 15) {
        $users = TESTBean::inst()->getUserIds($userId);
        foreach ($users as $uid) {
            UP_fromadmin::inst()->givePoints(PsUser::inst($uid), $cnt, getRandomString());
        }
    }

    /**
     * Привязывает все ячейки пользователя к картинке-мозайке
     */
    public final function bindAllUsersCells($imgId = 1, $userId = null) {
        foreach (TESTBean::inst()->getUserIds($userId) as $uId) {
            MosaicImage::inst($imgId)->bindAllUserCells($uId);
        }
    }

    /*
     * 
     * 
     * 
     * 
     * =========================
     *        КОММЕНТАРИИ
     * =========================
     * 
     * 
     * 
     * 
     */

    const RND_STRING_LEN = 20;

    private $postData = array();

    public function getText(PostsProcessor $processor, $postId, $takeTextFromPost) {
        if (!$takeTextFromPost) {
            return getRandomString(TestManager::RND_STRING_LEN);
        }

        $ident = $processor->getPostType() . '_' . $postId;

        $matches = array();
        if (array_key_exists($ident, $this->postData)) {
            $matches = $this->postData[$ident];
        } else {
            $content = $processor->getPostContentProvider($postId)->getPostContent()->getContent();
            preg_match_all("/<p[^>]*>([^<]*)<\/p>/si", $content, $matches, PREG_PATTERN_ORDER);
            $matches = $matches[1];
            $this->postData[$ident] = $matches;
        }

        $cnt = count($matches);
        $text = trim($cnt == 0 ? getRandomString(TestManager::RND_STRING_LEN) : $matches[rand(0, $cnt - 1)]);
        return $text ? UserInputTools::safeLongText($text) : getRandomString(TestManager::RND_STRING_LEN);
    }

    /**
     * Генерация комментариев к постам.
     * rootCount - кол-во root комментариев
     * childCount - кол-во дочерних комментариев, при этом они привязываются случайным образом
     * postType - если передан, то комментарии будут сгенерированы только к постам этого типа
     * postId - для генерации комменариев к конкретному посту
     * takeTextFromPost - признак, брать ли текст комментариев из тела поста
     */
    public final function generateComments($rootCount = 20, $childCount = 50, $postType = null, $postId = null, $takeTextFromPost = true) {
        $cproc = $this->getCommentsProcessor($postType);

        /* @var $proc PostsProcessor */
        foreach ($cproc as $proc) {
            $this->LOGGER->info("<<<CREATING COMMENTS FOR POSTS OF TYPE [" . $proc->getPostType() . "]>>>");

            if ($postId) {
                $posts[] = $proc->getPost($postId);
            } else {
                $posts = $proc->getPosts();
            }

            /* @var $post AbstractPost */
            foreach ($posts as $post) {
                $this->LOGGER->info("Creating comments for post " . $post->getPostType() . '|' . $post->getName());
                for ($i = 1; $i <= $rootCount; $i++) {
                    $proc->getDiscussionController()->saveMessage($post->getId(), null, $this->getText($proc, $post->getId(), $takeTextFromPost), null, PsUser::inst(TESTBean::inst()->getRandomUserId()));
                }

                for ($i = 1; $i <= $childCount; $i++) {
                    $commentsTable = $proc->dbBean()->getCommentsTable();
                    $parentId = TESTBean::inst()->getRandomCommentId($commentsTable, $post->getId());
                    $proc->getDiscussionController()->saveMessage($post->getId(), $parentId, $this->getText($proc, $post->getId(), $takeTextFromPost), null, PsUser::inst(TESTBean::inst()->getRandomUserId()));
                }
            }
        }
    }

    /**
     * Удаление всех комментариев ко всем постам.
     */
    public final function deleteAllComments($postType = null) {
        $cproc = Handlers::getInstance()->getCommentProcessors($postType);
        /* @var $proc CommentsProcessor */
        foreach ($cproc as $proc) {
            TESTBean::inst()->deleteAllComments($proc->dbBean()->getCommentsTable());
        }
    }

    /**
     * Генерация лайков к сообщениям дискуссий
     */
    public final function generateCommentLikes() {
        TESTBean::inst()->cleanVotes();

        $userIds = TESTBean::inst()->getUserIds();

        $controllers = Handlers::getInstance()->getDiscussionControllers();
        /** @var $ctrl DiscussionController */
        foreach ($controllers as $ctrt) {
            $settings = $ctrt->getDiscussionSettings();
            if (!$settings->isVotable()) {
                continue; //---
            }
            $messages = TESTBean::inst()->getAllMessages($settings);

            foreach ($messages as $msg) {
                $msgId = $msg[$settings->getIdColumn()];
                $threadUnique = $settings->getThreadUnique($msg[$settings->getThreadIdColumn()]);
                $authorId = $msg['id_user'];
                foreach ($userIds as $userId) {
                    if ($authorId == $userId) {
                        continue; //За свои сообщения не голосуем
                    }
                    $votes = rand(-1, 1);
                    if (!$votes) {
                        continue;
                    }
                    VotesManager::inst()->addVote($threadUnique, $msgId, $userId, $authorId, $votes);
                }
            }
        }
    }

    /*
     * 
     * 
     * 
     * 
     * =============================
     *        РУБРИКИ И ПОСТЫ
     * =============================
     * 
     * 
     * 
     * 
     */

    /**
     * Генерация тестовых постов и рубрик. 
     * Перед генерацией будут удалены предыдущие тестовые посты и рубрики.
     * 
     * rubricsCnt - кол-во рубрик, которое будет сгенерировано
     * postsCnt - общее кол-во постов, которое будет сгенерировано
     */
    public final function generateTestPostsAndRubrics($rubricsCnt = 10, $postsCnt = 30, $postType = null) {
        $this->deleteTestPostsAndRubrics($postType);

        $rproc = $this->getRubricsProcessor($postType);
        /* @var $rp RubricsProcessor */
        foreach ($rproc as $rp) {
            for ($index = 0; $index < $rubricsCnt; $index++) {
                $suffix = getRandomString();
                TESTBean::inst()->createRubric(
                        $rp->dbBean()->getRubricsTable(), //
                        getRandomString(15, true), //
                        "test_$suffix", //
                        getRandomString(1000, true)
                );
            }

            for ($index = 0; $index < $postsCnt; $index++) {
                $suffix = getRandomString();
                TESTBean::inst()->createPost(
                        $rp->dbBean()->getRubricsTable(), //
                        $rp->dbBean()->getPostsTable(), //
                        getRandomString(20, true), //
                        "test_$suffix", //
                        getRandomString(10000, true), //
                        getRandomString(1000, true));
            }
        }
    }

    /**
     * Удаление тестовых постов и рубрик
     */
    public final function deleteTestPostsAndRubrics($postType = null) {
        $this->deleteAllComments($postType);

        $proc = $this->getPostsProcessor($postType);
        /* @var $rp PostsProcessor */
        foreach ($proc as $rp) {
            TESTBean::inst()->deleteTestPosts($rp->dbBean()->getPostsTable());
        }

        $proc = $this->getRubricsProcessor($postType);
        /* @var $rp RubricsProcessor */
        foreach ($proc as $rp) {
            TESTBean::inst()->deleteTestRubrics($rp->dbBean()->getRubricsTable());
        }
    }

    /** @return TestManager */
    public static function inst() {
        return parent::inst();
    }

    protected function __construct() {
        //Разрешаем работать с классом только администратору
        AuthManager::checkAdminAccess();
        //Мы должны находиться не в продакшене
        PsDefines::assertProductionOff(__CLASS__);
        $this->LOGGER = PsLogger::inst(__CLASS__);
        $this->BEAN = TESTBean::inst();
    }

}

?>