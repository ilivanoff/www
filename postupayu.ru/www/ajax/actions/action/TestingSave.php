<?php

class TestingSave extends AbstractAjaxAction {

    protected function getAuthType() {
        return AuthManager::AUTH_TYPE_AUTHORIZED;
    }

    protected function isCheckActivity() {
        return false;
    }

    //Массивы не передаются, если они пусты
    protected function getRequiredParamKeys() {
        return array('id', /* 'tasks', */ 'time');
    }

    protected function executeImpl(ArrayAdapter $params) {
        $testingId = $params->int('id');
        $tasks = $params->arr('tasks');
        $time = $params->int('time');

        TestingManager::getInstance()->updateTestingResults($testingId, $time, $tasks);

        $pointsGiven = PL_testing::inst()->givePoints(PsUser::inst(), $testingId);

        return new AjaxSuccess($pointsGiven);
    }

}

?>