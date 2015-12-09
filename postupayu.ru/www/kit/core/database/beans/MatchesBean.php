<?php

class MatchesBean extends BaseBean {
    /*
     * Метод должен вернуть true в том случае, если ответ был привязан к пользователю.
     */

    public function registerAnswer($taskIdent, $matchesString, $userId = null) {
        $taskId = $this->getRec('select id_task from ps_matches_task where ident=?', $taskIdent);
        $solutionId = null;
        if ($taskId) {
            $taskId = $taskId['id_task'];
            $solutionId = $this->getRec('select id_solution from ps_matches_ans where id_task=? and v_solution=?', array($taskId, $matchesString));
        } else {
            $taskId = $this->insert('INSERT INTO ps_matches_task (ident) VALUES (?)', array($taskIdent));
        }

        $hasUserBind = false;
        if ($solutionId) {
            $solutionId = $solutionId['id_solution'];
            $hasUserBind = $userId ? $this->getCnt('select count(*) as cnt from ps_matches_ans2user where id_user=? and id_solution=?', array($userId, $solutionId)) > 0 : false;
        } else {
            $solutionId = $this->insert('INSERT INTO ps_matches_ans (id_task, v_solution, dt_event) VALUES (?, ?, unix_timestamp())', array($taskId, $matchesString));
        }

        if ($userId && !$hasUserBind) {
            $this->update('insert into ps_matches_ans2user (id_task, id_user, id_solution, dt_event) VALUES (?, ?, ?, unix_timestamp())', array($taskId, $userId, $solutionId));
            return true;
        }
        return false;
    }

    public function getSolvedTasksCnt($userId) {
        return $this->getCnt('SELECT count(1) as cnt FROM ps_matches_task t
 WHERE t.id_task IN (SELECT m.id_task FROM ps_matches_ans2user m WHERE m.id_user = ?)', $userId);
    }

    public function getTasksAnswers4User($userId) {
        $arr = $this->getArray('SELECT t.ident, a.v_solution FROM ps_matches_task t, ps_matches_ans a WHERE t.id_task = a.id_task
 and t.id_task in (SELECT m.id_task FROM ps_matches_ans2user m WHERE m.id_user = ?) order by a.dt_event', $userId);
        $res = array();
        foreach ($arr as $data) {
            $res[$data['ident']][] = $data['v_solution'];
        }
        return $res;
    }

    public function getTaskAnswers4User($userId, $taskIdent) {
        $arr = $this->getArray('
SELECT a.v_solution
  FROM ps_matches_ans a
 WHERE a.id_task in (
 select t.id_task from ps_matches_task t, ps_matches_ans2user m
 where t.id_task = m.id_task and m.id_user=? and t.ident=?) ORDER BY a.dt_event', array($userId, $taskIdent));
        $res = array();
        foreach ($arr as $data) {
            $res[$taskIdent][] = $data['v_solution'];
        }
        return $res;
    }

    /*
     * СИНГЛТОН
     */

    /** @return MatchesBean */
    public static function inst() {
        return parent::inst();
    }

}

?>
