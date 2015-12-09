<?php

class UP_pattern extends AbstractPointsGiver {

    public function givePointsImpl(GivePointsContext $ctxt, $param1 = null, $param2 = null) {
        //Проверяем, можно ли дать пользователю очки
    }

    public function checkPointsImpl(GivePointsContext $ctxt) {
        //Проверяем, не забыли ли дать пользователю очки
    }

    public function shortReason(UserPointDO $point) {
        return 'Краткое описание причины выдачи очков';
    }

    public function fullReason(UserPointDO $point) {
        return 'Полное описание причины выдачи очков';
    }

}

?>