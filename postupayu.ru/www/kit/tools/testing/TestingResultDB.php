<?php

class TestingResultDB extends BaseDataStore {

    private $testing;

    public function __construct(array $data, TestingDB $testing) {
        parent::__construct($data);
        $this->testing = $testing;
    }

    public function getTestingResultId() {
        return (int) $this->id_testing_result;
    }

    /*
     * Время в секундах
     */

    public function getTime() {
        return (int) $this->n_time;
    }

    private $tasks = array();

    public function getTasks() {
        return $this->tasks;
    }

    public function addTask($taskNum) {
        $taskNum = (int) $taskNum;
        return $this->tasks[$taskNum] = $taskNum;
    }

    /*
     * Утилитные функции
     */

    public function isPassed($taskNum) {
        $taskNum = (int) $taskNum;
        return array_key_exists($taskNum, $this->tasks);
    }

    public function getPassedCnt() {
        return count($this->tasks);
    }

    public function getPercent() {
        $passed = $this->getPassedCnt();
        $total = $this->testing->getTasksCnt();
        return round(($passed / $total) * 100);
    }

    /*
     * Возвращает текстовое представление результата теста в виде:
     * 7 из 20 (35%)
     */

    public function asString() {
        $passed = $this->getPassedCnt();
        $total = $this->testing->getTasksCnt();
        $pcnt = $total ? round(($passed / $total) * 100) : 0;
        return "$passed из $total ($pcnt%)";
    }

    /*
     * Возвращает графическое представление результата теста в виде:
     * +--+-+-+++-+-++
     */

    public function asStringGr() {
        $result = '';
        for ($index = 1; $index <= $this->testing->getTasksCnt(); $index++) {
            $result.=$this->isPassed($index) ? '+' : '&minus;';
        }
        return $result;
    }

}

?>
