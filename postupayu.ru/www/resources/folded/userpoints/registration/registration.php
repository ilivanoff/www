<?php

class UP_registration extends AbstractPointsGiver {

    public function givePointsImpl(GivePointsContext $ctxt) {
        return $this->doGivePoints($ctxt);
    }

    public function checkPointsImpl(GivePointsContext $ctxt) {
        return $this->doGivePoints($ctxt);
    }

    public function shortReason(UserPointDO $point) {
        return 'Регистрация';
    }

    public function fullReason(UserPointDO $point) {
        return '';
    }

    public function doGivePoints(GivePointsContext $ctxt) {
        $ctxt->givePoints(5, null, true);
    }

}

?>