<?php

/**
 * @author Admin
 */
class AdminBean extends BaseBean {

    private $adminId;

    public function getPostsWithUncheckedComments() {
        $beans = array(ISBean::inst(), BPBean::inst(), TRBean::inst());

        $result = array();

        /** @var PostsBean */
        foreach ($beans as $bean) {
            $table = $bean->getPostsTable();
            $table_comments = $bean->getCommentsTable();
            $query = "SELECT p.*, (SELECT count(*)
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

            $posts = $this->getArray($query, array($this->adminId, $this->adminId));

            foreach ($posts as $postDataArr) {
                $result[$bean->getPostType()][] = new Post($bean->getPostType(), $postDataArr);
            }
        }

        return $result;
    }

    /*
     * Защищаемся от получения двух экземпляров.
     */

    private static $cnt = 0;

    public function __construct($parentClass, $userId) {
        check_condition($parentClass === 'AdminManager' && ++self::$cnt === 1, 'Trying to create one more instance of ' . __CLASS__);
        check_condition(UserBean::inst()->isAdmin($userId), "User [$userId] is not admin");
        $this->adminId = $userId;
    }

}

?>
