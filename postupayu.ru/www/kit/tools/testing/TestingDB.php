<?php

class TestingDB extends BaseDataStore {

    public function getTestingId() {
        return (int) $this->id_testing;
    }

    public function getName() {
        return $this->name;
    }

    public function getTasksCnt() {
        return (int) $this->n_tasks;
    }

    /*
     * Время в минутах
     */

    public function getTime() {
        return (int) $this->n_time;
    }

}

?>
