<?php

class Post extends AbstractPost implements NewsEventInterface {

    public function __construct($type, $data) {
        parent::__construct($type, $data);
    }

    public function getId() {
        return (int) $this->id_post;
    }

    public function getName() {
        return $this->name;
    }

    public function getRubricId() {
        return $this->hasKey('id_rubric') ? (int) $this->id_rubric : null;
    }

    public function getRubricName() {
        return $this->rubric_name;
    }

    public function getDtPublication() {
        return $this->dt_publication;
    }

    public function getDtEvent($format = DF_POSTS) {
        return DatesTools::inst()->uts2dateInCurTZ($this->dt_publication, $format);
    }

    public function getRevCount() {
        return $this->rev_count;
    }

    public function incRevCount() {
        ++$this->rev_count;
    }

    public function getCommentsCount() {
        return $this->comments_count;
    }

    public function getContent() {
        return $this->content;
    }

    public function getShowcase() {
        return $this->content_showcase;
    }

    public function isTpl() {
        return $this->hasKey('b_tpl') && !!$this->b_tpl;
    }

    /**
     * Метод возвращает оригинальную запись из базы для данного поста
     * @return array
     */
    public function getDbRow() {
        return $this->getData();
    }

    /**
     * Метод возвращает признак, является ли пост виртуальным
     * @return bool
     */
    public function isVirtual() {
        return $this->getId() == TEST_ENTITY_ID;
    }

    public function getNewsEventUnique() {
        return $this->getId();
    }

    public function getNewsEventUtc() {
        return $this->getDtPublication();
    }

}

?>