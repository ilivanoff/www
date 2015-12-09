<?php

class GymProgrammEx {

    private $data;
    private $sets = array();

    public function __construct(array $data) {
        $this->data = $data;

        if (!isEmptyInArray('sets', $this->data)) {
            foreach ($this->data['sets'] as $setValue) {
                $this->addSet($setValue);
            }
        }
    }

    public function getId() {
        return isEmptyInArray('id', $this->data) ? null : (int) $this->data['id'];
    }

    public function getName() {
        return isEmptyInArray('name', $this->data) ? null : $this->data['name'];
    }

    public function getComment() {
        return isEmptyInArray('comment', $this->data) ? null : $this->data['comment'];
    }

    public function getSets() {
        return $this->sets;
    }

    public function addSet($value) {
        if (!isEmpty($value)) {
            $this->sets[] = trim($value);
        }
    }

}

?>
