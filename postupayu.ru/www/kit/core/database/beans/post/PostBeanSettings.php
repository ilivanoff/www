<?php

/**
 * Настройки бина для рубрик/постов/комментариям к постам
 *
 * @author azazello
 */
final class PostBeanSettings {

    private $postType;
    private $postsTable;
    private $rubricsTable;
    private $commentsTable;

    public function __construct($postType, $commentsTable, $postsTable, $rubricsTable = null) {
        $this->postType = PsCheck::notEmptyString($postType);
        $this->postsTable = PsCheck::notEmptyString($postsTable);
        $this->rubricsTable = $rubricsTable;
        $this->commentsTable = PsCheck::notEmptyString($commentsTable);
    }

    public function getPostType() {
        return $this->postType;
    }

    public function getPostsTable() {
        return $this->postsTable;
    }

    public function getPostsView() {
        return 'v_' . $this->postsTable;
    }

    public function getRubricsTable() {
        return $this->rubricsTable;
    }

    public function getRubricsView() {
        return $this->rubricsTable ? 'v_' . $this->rubricsTable : null;
    }

    public function getCommentsTable() {
        return $this->commentsTable;
    }

    public function isRubricable() {
        return PsCheck::isNotEmptyString($this->rubricsTable);
    }

    public static function getClass() {
        return __CLASS__;
    }

}

?>