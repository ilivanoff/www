<?php

class GymGr extends BaseDataStore {

    public function getId() {
        return (int) $this->gr_id;
    }

    public function getName() {
        return $this->gr_name;
    }

    /*
     * Упражнения для этой группы мышц
     */

    private $exes = array();

    public function addEx(GymEx $ex) {
        $this->exes[] = $ex;
    }

    public function getExercises() {
        return $this->exes;
    }

}

?>
