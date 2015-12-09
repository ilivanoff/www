<?php

class IP_userpoints extends BaseOfficePage implements NumerableOfficePage {

    public function getTitle() {
        return 'Ваши баллы';
    }

    public function getNumericState() {
        $count = UserPointsManager::inst()->getNewPointsCnt(PsUser::inst());
        return $count > 0 ? "+$count" : null;
    }

    protected function processRequest(ArrayAdapter $params) {
        $UPM = UserPointsManager::inst();
        //При открытии страницы отмечаем, что пользователь знает обо всех очках
        $UPM->markUserPointsShown(PsUser::inst());
        //Получим все очки, выданные пользователю
        return new IdentPageFilling(array('points' => $UPM->getAllUserPoints(PsUser::inst())));
    }

}

?>