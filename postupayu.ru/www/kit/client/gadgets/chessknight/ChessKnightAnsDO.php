<?php

class ChessKnightAnsDO extends BaseDataStore {

    private $system, $answer;

    public function __construct($system, $answer) {
        $this->system = $system;
        $this->answer = $answer;
    }

    public function isSystem() {
        return $this->system;
    }

    public function getAnswer() {
        return $this->answer;
    }

}

?>
