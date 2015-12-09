<?php

/**
 * Базовый класс для всех классов фолдинга UserPointsManager, позволяющий выдавать очки пользователям.
 *
 * @author azazello
 */
abstract class AbstractPointsGiver extends FoldedClass implements PointsGiver, PointsGiverMaster {

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    protected function _construct() {
        
    }

    /** @return AbstractPointsGiver */
    public final static function inst() {
        return parent::inst();
    }

    /**
     * Метод, вызываемый для выдачи очков пользователю.
     */
    public final function givePoints(PsUser $user, $param1 = null, $param2 = null) {
        return UserPointsManager::inst()->givePoints($this, func_get_args());
    }

    /**
     * Метод, вызываемый для проверки, нет ли у пользователя очков, положенных, 
     * но не выданных ему.
     */
    public final function checkPoints(PsUser $user) {
        return UserPointsManager::inst()->checkPoints($this, $user);
    }

}

?>