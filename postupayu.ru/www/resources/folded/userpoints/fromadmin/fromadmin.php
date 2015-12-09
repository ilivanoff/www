<?php

class UP_fromadmin extends AbstractPointsGiver {

    public function givePointsImpl(GivePointsContext $ctxt, $cnt = null, $reason = null) {
        $ctxt->givePoints($cnt, $reason);
    }

    public function checkPointsImpl(GivePointsContext $ctxt) {
        
    }

    public function shortReason(UserPointDO $point) {
        return 'Выдано администратором';
    }

    public function fullReason(UserPointDO $point) {
        return $point->getData();
    }

}

?>