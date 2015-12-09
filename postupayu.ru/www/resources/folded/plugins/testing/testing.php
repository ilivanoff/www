<?php

/**
 * Плагин - тестирование.
 */
class PL_testing extends BasePlugin implements PointsGiver {

    const PASS_THRESHOLD_PCNT = 80;

    public function getName() {
        return 'Тестирование';
    }

    public function getPluginContent($content, ArrayAdapter $params, PluginFetchingContext $ctxt) {
        $id = $params->int('id');
        $name = $params->str('test_name');
        $minutes = $params->int('time');

        $tasksCnt = $ctxt->getTasksCount();

        $result = null;
        $testing = null;
        if ($id) {
            $testing = TestingBean::inst()->updateTestingState($id, $name, $tasksCnt, $minutes);
            $result = TestingManager::getInstance()->getTestingResults($testing->getTestingId());
        }

        $tplData = $params->getData();

        $tplData['testing'] = $testing;
        $tplData['tasks'] = $content;
        $tplData['results'] = $result;
        $tplData['tasks_cnt'] = $tasksCnt;

        $content = $this->getFoldedEntity()->fetchTpl($tplData);
        $data = $testing ? $testing->getTestingId() : null;

        return new PluginContent($content, $data);
    }

    /** Видимость для popup */
    public function getPopupVisibility() {
        //Нельзя вот так просто взять и перейти на плагин с тестированием - нужны же данные:)
        return PopupVis::FALSE;
    }

    /*
     * ОЧКИ
     */

    public function givePointsImpl(GivePointsContext $ctxt, $testingId = null) {
        $this->tryGivePoints($ctxt, $testingId);
    }

    public function checkPointsImpl(GivePointsContext $ctxt) {
        foreach (TestingBean::inst()->getUserTestings($ctxt->getUserId()) as $testingId) {
            $this->tryGivePoints($ctxt, $testingId);
        }
    }

    public function shortReason(UserPointDO $point) {
        $name = TestingBean::inst()->getTestingById($point->getData())->getName();
        return "Успешно пройден тест \"$name\"";
    }

    public function fullReason(UserPointDO $point) {
        return '';
    }

    private function tryGivePoints(GivePointsContext $ctxt, $testingId) {
        if ($ctxt->hasPoints($testingId)) {
            return; //---
        }

        $testResult = TestingBean::inst()->getTestingResult($testingId, $ctxt->getUserId());

        if ($testResult && $testResult->getPercent() >= self::PASS_THRESHOLD_PCNT) {
            $ctxt->givePoints(5, $testingId);
        }
    }

}

?>