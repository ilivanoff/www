<?php

/**
 * Базовый бин для работы с занятиями кружка
 *
 * @author azazello
 */
final class TRBean extends RubricsBean {

    protected function PostBeanSettings() {
        return new PostBeanSettings(POST_TYPE_TRAINING, 'train_post_comments', 'train_post', 'train_rubric');
    }

    public function getPassedLessons($userId) {
        return $this->getIds('select id_post as id from user_lessons where id_user=?', $userId);
    }

    public function toggleLessonState($userId, $lessonId) {
        $deleted = $this->update('delete from user_lessons where id_user=? and id_post=?', array($userId, $lessonId));
        if ($deleted == 0) {
            $this->update('insert into user_lessons (id_user, id_post) values(?, ?)', array($userId, $lessonId));
        }
    }

    /** @return TRBean */
    public static function inst() {
        return parent::inst();
    }

}

?>