<?php

class Rubric extends AbstractRubric {

    public function getId() {
        return (int) $this->id_rubric;
    }

    public function getPartitionId() {
        return (int) $this->id_partition;
    }

    public function getName() {
        return $this->name;
    }

    public function getContent() {
        return $this->content;
    }

    public function isTpl() {
        return !!$this->b_tpl;
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

}

?>