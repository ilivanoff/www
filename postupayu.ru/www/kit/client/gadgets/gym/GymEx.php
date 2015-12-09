<?php

class GymEx extends BaseDataStore {

    public function getId() {
        return (int) $this->ex_id;
    }

    public function getName() {
        return $this->ex_name;
    }

    /*
     * Группы мышц, на которые действует упражнение
     */

    private $groups = array();

    public function addGroup(GymGr $gr) {
        $this->groups[] = $gr;
    }

    public function getGroups() {
        return $this->groups;
    }

}

?>
