<?php

abstract class BasePlugin extends FoldedClass implements PointsGiverMaster {

    protected function _construct() {
        //do nothing...
    }

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    public abstract function getName();

    public function getDescr() {
        return $this->getName();
    }

    /** @return BasePlugin */
    public final static function inst() {
        return parent::inst();
    }

    /** @return PluginContent */
    public abstract function getPluginContent($content, ArrayAdapter $params, PluginFetchingContext $ctxt);

    /** Видимость для popup */
    public function getPopupVisibility() {
        return PopupVis::BYPOST;
    }

    /**
     * Метод, вызываемый для выдачи очков пользователю.
     */
    public final function givePoints(PsUser $user, $param1 = null, $param2 = null) {
        return UserPointsManager::inst()->givePoints($this, func_get_args());
    }

    /**
     * Метод, вызываемый для проверки, нет ли у пользователя очков, положенных, но не выданных ему.
     */
    public final function checkPoints(PsUser $user) {
        return UserPointsManager::inst()->checkPoints($this, $user);
    }

}

?>