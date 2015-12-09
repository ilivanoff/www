<?php

class LibItemDb extends BaseDataStore {

    public function getId() {
        return $this->id;
    }

    public function getGroup() {
        return $this->grup;
    }

    public function getName() {
        return $this->name;
    }

    public function getIdent() {
        return $this->ident;
    }

    public function getContent() {
        return $this->content;
    }

    public function getDtStart() {
        return $this->dt_start;
    }

    public function getDtStop() {
        return $this->dt_stop;
    }

    public function isShow() {
        return !!$this->b_show;
    }

    /**
     * Проверяет, установлен ли Id для сущности - это позволит определить тип действия
     */
    public function hasId() {
        return $this->hasKey('id') && is_numeric($this->id);
    }

}

?>