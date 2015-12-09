<?php

/**
 * Менеджер для работы с голосами за разные сущности сиситемы
 *
 * @author azazello
 */
final class VotesManager extends AbstractSingleton {

    const CACHE_USER = 'user';
    const CACHE_TOTAL = 'total';

    /** @var array */
    private $CACHE;

    /** @var VotesBean */
    private $BEAN;

    public function enableCached($group) {
        if (!array_key_exists($group, $this->CACHE)) {
            $this->CACHE[$group] = array(
                self::CACHE_USER => array()
            );
        }
    }

    private function getVotesImpl($group, $inst, $userId, $default = 0) {
        if (!array_key_exists($group, $this->CACHE)) {
            return $userId ? $this->BEAN->getUserVotes($group, $inst, $userId, $default) : $this->BEAN->getVotesCount($group, $inst, $default);
        }
        if ($userId) {
            //User cache
            if (!array_key_exists($userId, $this->CACHE[$group][self::CACHE_USER])) {
                $this->CACHE[$group][self::CACHE_USER][$userId] = $this->BEAN->getUserVotes4Group($group, $userId);
            }
            return array_get_value($inst, $this->CACHE[$group][self::CACHE_USER][$userId], $default);
        } else {
            //Total cache
            if (!array_key_exists(self::CACHE_TOTAL, $this->CACHE[$group])) {
                $this->CACHE[$group][self::CACHE_TOTAL] = $this->BEAN->getVotesCount4Group($group);
            }
            return array_get_value($inst, $this->CACHE[$group][self::CACHE_TOTAL], $default);
        }
    }

    /**
     * ОСНОВНЫЕ МЕТОДЫ
     */
    public function removeVote($group, $inst, $userId) {
        $this->BEAN->removeVote($group, $inst, $userId);
    }

    public function addVote($group, $inst, $userId, $userToId, $votes) {
        $this->BEAN->addVote($group, $inst, $userId, $userToId, $votes);
    }

    public function getUserVotes($group, $inst, $userId, $default = 0) {
        return $this->getVotesImpl($group, $inst, $userId, $default);
    }

    public function getVotesCount($group, $inst, $default = 0) {
        return $this->getVotesImpl($group, $inst, null, $default);
    }

    /** @return VotesManager */
    public static function inst() {
        return parent::inst();
    }

    protected function __construct() {
        $this->CACHE = array();
        $this->BEAN = VotesBean::inst();
    }

}

?>