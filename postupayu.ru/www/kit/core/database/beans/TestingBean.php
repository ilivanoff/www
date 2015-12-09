<?php

/**
 * Description of UNBean
 *
 * @author Admin
 */
class TestingBean extends BaseBean {

    /** @return TestingDB */
    public function getTestingById($idTesting) {
        return new TestingDB($this->getRecEnsure('select * from ps_testing where id_testing=?', $idTesting));
    }

    /** @return TestingDB */
    public function updateTestingState($id, $name, $tasksCnt, $minutes) {
        check_condition($id, 'Testing id cannot be empty');
        $id = (int) $id;
        $cnt = $this->getCnt('select count(1) as cnt from ps_testing t where t.id_testing=?', $id);
        if ($cnt <= 0) {
            $this->insert('insert into ps_testing (id_testing, name, n_tasks, n_time) VALUES (?, ?, ?, ?)', array($id, $name, $tasksCnt, $minutes));
        } else {
            $this->update('update ps_testing SET name = ?, n_tasks = ?, n_time = ? WHERE id_testing = ?', array($name, $tasksCnt, $minutes, $id));
        }
        return $this->getTestingById($id);
    }

    private function deleteTestingResultContent($idTestingResult) {
        $this->update('delete from ps_testing_result_content where id_testing_result=?', $idTestingResult);
    }

    public function updateTestingResults($idTesting, $userId, $seconds, array $tasks) {
        $testing = $this->getTestingById($idTesting);

        $idTestingResult = $this->getRec('select r.id_testing_result from ps_testing_results r where id_testing=? and id_user=?', array($idTesting, $userId));
        if ($idTestingResult == null) {
            $idTestingResult = $this->insert('insert into ps_testing_results (id_testing, id_user, n_time) VALUES (?, ?, ?)', array($idTesting, $userId, $seconds));
        } else {
            $idTestingResult = (int) $idTestingResult['id_testing_result'];
            $this->update('update ps_testing_results SET n_time = ? WHERE id_testing_result = ?', array($seconds, $idTestingResult));
            $this->deleteTestingResultContent($idTestingResult);
        }

        foreach ($tasks as $task) {
            if ($task >= 1 && $task <= $testing->getTasksCnt()) {
                $this->update('insert into ps_testing_result_content (id_testing_result, n_task) values (?, ?)', array($idTestingResult, $task));
            }
        }
    }

    /** @return TestingResultDB */
    public function getTestingResult($idTesting, $userId) {
        $data = $this->getRec('select * from ps_testing_results r where id_testing=? and id_user=?', array($idTesting, $userId));
        if (!$data) {
            return null;
        }

        $testingResult = new TestingResultDB($data, $this->getTestingById($idTesting));

        $tasks = $this->getArray('select c.n_task from ps_testing_result_content c where c.id_testing_result=?', $testingResult->getTestingResultId());
        foreach ($tasks as $task) {
            $testingResult->addTask($task['n_task']);
        }

        return $testingResult;
    }

    public function dropTestingResults($idTestingRes, $userId) {
        $res = $this->getRec('select id_testing_result FROM ps_testing_results where id_user=? and id_testing_result=?', array($userId, $idTestingRes));
        check_condition($res != null, "Testing result with id $idTestingRes is not belongs to user with id $userId");
        $this->deleteTestingResultContent($idTestingRes);
        $this->update('delete from ps_testing_results where id_testing_result=?', $idTestingRes);
    }

    //Возвращает список тестов, которые прощёл пользователь
    public function getUserTestings($userId) {
        return $this->getIds('select distinct(id_testing) as id from ps_testing_results where id_user=?', $userId);
    }

    /*
     * СИНГЛТОН
     */

    /** @return TestingBean */
    public static function inst() {
        return parent::inst();
    }

}

?>
