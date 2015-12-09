<?php

class UserPointsBean extends BaseBean {

    /**
     * Возвращает очки по коду
     * 
     * @param int|null $userId - если передан код пользователя, то стсиема убедится, что очки даны этому пользователю
     * @return UserPointDO
     */
    public function getPointById($pointId, $userId = null) {
        $where['id_point'] = $pointId;
        if (is_inumeric($userId)) {
            $where['id_user'] = $userId;
        }
        return new UserPointDO($this->getRecEnsure(Query::select('*', 'ps_user_points', $where)));
    }

    /**
     * Возвращает все очки пользователя, начиная с последнего выданного
     */
    public function getAllUserPoints($userId) {
        return $this->getObjects('SELECT * FROM ps_user_points where id_user=? order by dt_event desc, id_point desc', $userId, 'UserPointDO');
    }

    public function hasPointsWithData($userId, $reasonId, $data) {
        $where['id_user'] = $userId;
        $where['id_reason'] = $reasonId;

        if ($data === null) {
            $where[] = 'v_data is null';
        } else {
            $where[] = 'v_data is not null';
            $where['v_data'] = $data;
        }

        return $this->hasRec('ps_user_points', $where);
    }

    /**
     * Выдача очков пользователю. Если очки будут успешно выданы, вернётся созданная в базе запись,
     * иначе - null
     * 
     * @return UserPointDO
     */
    public function givePoints($userId, $reasonId, $cnt, $data) {
        if ($this->hasPointsWithData($userId, $reasonId, $data)) {
            return null;
        }

        $cnt = is_numeric($cnt) ? 1 * $cnt : null;
        check_condition(is_integer($cnt) && $cnt > 0, "Нельзя выдать [$cnt] очков");

        $pointId = $this->insert('INSERT INTO ps_user_points (id_user, id_reason, n_cnt, v_data, dt_event) VALUES (?, ?, ?, ?, unix_timestamp())', array($userId, $reasonId, $cnt, $data));
        return new UserPointDO($this->getRecEnsure('SELECT * FROM ps_user_points where id_point=?', $pointId));
    }

    public function getNewPointsCnt($userId) {
        return $this->getCnt('select ifnull(sum(n_cnt), 0) as cnt from ps_user_points where id_user=? and b_shown=0', $userId);
    }

    public function setPointsShown($userId) {
        $this->update('update ps_user_points set b_shown=1 where id_user=?', $userId);
    }

    public function getPointsCnt($userId) {
        return $this->getCnt('SELECT ifnull(sum(n_cnt), 0) as cnt FROM ps_user_points WHERE id_user = ?', $userId);
    }

    /** @return UserPointsBean */
    public static function inst() {
        return parent::inst();
    }

}

?>