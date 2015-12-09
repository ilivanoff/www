<?php

class GymProgramm {

    private $data;
    private $exercises = array();

    public function __construct(array $data) {
        $this->data = $data;

        if (!isEmptyInArray('datas', $this->data)) {
            foreach ($this->data['datas'] as $ex) {
                $this->exercises[] = new GymProgrammEx($ex);
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

    public function getExercises() {
        return $this->exercises;
    }

    public function hasExercises() {
        return !empty($this->exercises);
    }

    public function addExercise($progEx, GymProgrammEx $ex) {
        return $this->exercises[$progEx] = $ex;
    }

    public function getExercise($progEx) {
        return array_key_exists($progEx, $this->exercises) ? $this->exercises[$progEx] : null;
    }

    /*
      var programm = {
      id: null,
      name: nameValue,
      comment: null,
      datas: []
      }

      var data = {};
      data.id = null;
      data.name = null;
      data.sets = [];
      data.comment = null;
     */

    public function toArray() {
        $programm = array(
            'id' => $this->getId(),
            'name' => $this->getName(),
            'comment' => $this->getComment(),
            'datas' => array()
        );

        /* @var $exercise GymProgrammEx */
        foreach ($this->getExercises() as $exercise) {
            $programm['datas'][] = array(
                'id' => $exercise->getId(),
                'name' => $exercise->getName(),
                'comment' => $exercise->getComment(),
                'sets' => $exercise->getSets()
            );
        }

        return $programm;
    }

}

?>
