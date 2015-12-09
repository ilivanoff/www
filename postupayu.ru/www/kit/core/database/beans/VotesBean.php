<?php

/**
 * Бин для работы с голосами пользователей за разные сущности в системе.
 * Принцип - один пользователь - один голос за запись.
 *
 * @author azazello
 */
class VotesBean extends BaseBean {

    public function removeVote($group, $inst, $userId) {
        $this->update('delete from ps_votes where v_group=? and id_inst=? and id_user=?', array($group, $inst, $userId));
    }

    public function addVote($group, $inst, $userId, $userToId, $votes) {
        check_condition(!$userToId || ($userToId != $userId), 'Запрещено голосовать за своё сообщение');
        $this->removeVote($group, $inst, $userId);
        $this->update('insert into ps_votes (v_group, id_inst, id_user, id_user_to, n_votes) values (?,?,?,?,?)', array($group, $inst, $userId, $userToId, $votes));
    }

    public function getUserVotes($group, $inst, $userId, $default = 0) {
        $rec = $this->getRec('select n_votes from ps_votes where v_group=? and id_inst=? and id_user=?', array($group, $inst, $userId));
        return is_array($rec) ? 1 * $rec['n_votes'] : $default;
    }

    public function getUserVotes4Group($group, $userId) {
        return $this->getMap('select id_inst as id, n_votes as value from ps_votes where v_group=? and id_user=?', array($group, $userId));
    }

    public function getVotesCount($group, $inst, $default = 0) {
        return $this->getCnt('select ifnull(SUM(n_votes), ?) as cnt from ps_votes where v_group=? and id_inst=?', array($default, $group, $inst));
    }

    public function getVotesCount4Group($group) {
        return $this->getMap('select id_inst as id, SUM(n_votes) as value from ps_votes where v_group=? group by id_inst', $group);
    }

    /** @return VotesBean */
    public static function inst() {
        return parent::inst();
    }

}

?>