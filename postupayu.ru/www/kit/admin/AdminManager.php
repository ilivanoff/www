<?php

/**
 * Классом можно пользоваться только когда пользователь авторизовался под администратором.
 * Класс содержит методы, доступные аднимистратору из обычного интерфейса (а не из админки).
 * 
 * Класс наследует базовый бин, поэтому из него сразу можно работать с базой.
 */
class AdminManager extends BaseBean {

    /**
     * Метод строит список постов, в которых появились новые комментарии, и показывает их администратору.
     * 
     * Выпуск №1 (3)
     * Архимед (7)
     */
    public function getPostsWithUncheckedCommentsHtml() {
        $adminId = AuthManager::getUserId();

        $result = array();

        /* @var $pp PostsProcessor */
        foreach (Handlers::getInstance()->getPostsProcessors() as $pp) {
            $bean = $pp->dbBean();
            $table = $bean->getPostsTable();
            $table_comments = $bean->getCommentsTable();
            $query = "SELECT p.id_post, (SELECT count(*)
             FROM $table_comments c
            WHERE     c.id_post = p.id_post
                  AND c.b_deleted = 0
                  AND c.b_confirmed = 0
                  AND c.id_user <> ?) as comments_count
  FROM v_$table p
 WHERE EXISTS
          (SELECT *
             FROM $table_comments c
            WHERE     c.id_post = p.id_post
                  AND c.b_deleted = 0
                  AND c.b_confirmed = 0
                  AND c.id_user <> ?)";

            $posts = $this->getArray($query, array($adminId, $adminId));

            foreach ($posts as $post) {
                $result[] = '<li>' . $pp->postHref($post['id_post'], null, 'comments') . ' (' . $post['comments_count'] . ')</li>';
            }
        }

        return '<ul>' . implode('', $result) . '</ul>';
    }

    /** @return AdminManager */
    public static function inst() {
        AuthManager::checkAdminAccess();
        return parent::inst();
    }

}

?>