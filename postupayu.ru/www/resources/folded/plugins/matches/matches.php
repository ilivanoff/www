<?php

/**
 * Плагин - раскладывание спичек.
 */
class PL_matches extends BasePlugin implements PointsGiver {

    public function getName() {
        return 'Задачи на раскладывание спичек';
    }

    public function getDescr() {
        return "Реализация задачи на раскладывание спичек. Суть её в том, чтобы получить необходимую конфигурацию спичек путём убирания некоторых из них.";
    }

    public function getPluginContent($content, ArrayAdapter $params, PluginFetchingContext $ctxt) {
        $data['tasks'] = MatchesManager::getInstance()->tasksHtml();
        return new PluginContent($this->getFoldedEntity()->fetchTpl($data));
    }

    /*
     * ВЫДАЧА ОЧКОВ
     */

    public function givePointsImpl(GivePointsContext $ctxt) {
        $this->doGivePoints($ctxt);
    }

    public function checkPointsImpl(GivePointsContext $ctxt) {
        $this->doGivePoints($ctxt);
    }

    public function shortReason(UserPointDO $point) {
        return 'Решены все задачи со спичками';
    }

    public function fullReason(UserPointDO $point) {
        return '';
    }

    private function doGivePoints(GivePointsContext $ctxt) {
        if (MatchesManager::getInstance()->isUserSolweAllTasks($ctxt->getUserId())) {
            $ctxt->givePoints(10);
        }
    }

}

?>
