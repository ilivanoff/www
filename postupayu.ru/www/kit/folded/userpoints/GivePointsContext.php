<?php

/**
 * Контекст для выдачи отков пользователю
 *
 * @author azazello
 */
class GivePointsContext {

    /** @var Код пользователя */
    private $userId;

    /** @var Код причины выдачи очков */
    private $reasonId;

    /** Признак - были ли даны очки пользователю */
    private $givenPoints = array();

    public function __construct($userId, $reasonId) {
        $this->userId = $userId;
        $this->reasonId = $reasonId;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function getGivenPoints() {
        return $this->givenPoints;
    }

    public function hasPoints($data = null) {
        return UserPointsBean::inst()->hasPointsWithData($this->userId, $this->reasonId, $data);
    }

    /**
     * 
     * @param type $cnt - кол-во очков
     * @param type $data - данные, которые нужно сохранить, чтобы потом декодировать текст
     * @param type $silently - признак, нужно ли посылать пользователю уведомление о выдаче этих очков
     */
    public function givePoints($cnt, $data = null, $silently = false) {
        $pointDo = UserPointsBean::inst()->givePoints($this->userId, $this->reasonId, $cnt, $data);
        if (!$silently && ($pointDo instanceof UserPointDO)) {
            $this->givenPoints[] = $pointDo;
        }
    }

    public function __toString() {
        return 'GivePointsContext for reasonId: ' . $this->reasonId;
    }

}

?>
