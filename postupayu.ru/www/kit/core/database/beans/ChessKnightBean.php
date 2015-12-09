<?php

class ChessKnightBean extends BaseBean {

    private function getSolutionId($hodesString) {
        $rec = $this->getRec('select id_solution from ps_chess_knight where v_solution=?', $hodesString);
        return $rec ? (int) $rec['id_solution'] : null;
    }

    private function hasUserBind($solutionId, $userId) {
        return $this->getCnt('select count(1) as cnt from ps_chess_knight2user where id_solution=? and id_user=?', array($solutionId, $userId)) > 0;
    }

    /*
     * Метод должен вернуть true в том случае, если ответ был привязан к пользователю.
     */

    public function registerAnswer($hodesString, $userId = null) {
        check_condition(strlen($hodesString) == 128, 'Длина строки с решением задачи о ходе коня не равна 128');

        $solutionId = $this->getSolutionId($hodesString);
        $hasUserBind = false;

        if ($solutionId === null) {
            /* До этого такого решения небыло */
            $solutionId = $this->insert('insert into ps_chess_knight (v_solution, b_system) VALUES (?, 0)', array($hodesString));
        } else if ($userId) {
            $hasUserBind = $this->hasUserBind($solutionId, $userId);
        }

        if ($userId && !$hasUserBind) {
            $this->update('insert into ps_chess_knight2user (id_user, id_solution, dt_event) VALUES (?, ?, unix_timestamp())', array($userId, $solutionId));
            return true;
        }

        return false;
    }

    public function getSystemSolutions($userId = null) {
        $dataArr = $this->getArray('select v_solution from ps_chess_knight where b_system=1 order by id_solution asc');

        $result = array();
        foreach ($dataArr as $value) {
            $result[] = new ChessKnightAnsDO(true, $value['v_solution']);
        }

        if ($userId) {
            $dataArr = $this->getArray('select v_solution from ps_chess_knight k, ps_chess_knight2user u where k.id_solution = u.id_solution and k.b_system=0 and u.id_user=? order by u.dt_event asc, u.id_solution asc', $userId);
            foreach ($dataArr as $value) {
                $result[] = new ChessKnightAnsDO(false, $value['v_solution']);
            }
        }

        return $result;
    }

    public function getUserSolvedCnt($userId) {
        return $this->getCnt('select count(1) as cnt from ps_chess_knight k, ps_chess_knight2user u where k.id_solution=u.id_solution and k.b_system=0 and u.id_user=?', array($userId));
    }

    /*
     * СИНГЛТОН
     */

    /** @return ChessKnightBean */
    public static function inst() {
        return parent::inst();
    }

}

?>
