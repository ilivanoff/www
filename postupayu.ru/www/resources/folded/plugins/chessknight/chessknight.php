<?php

/**
 * Плагин - задача о ходе шахматного коня.
 */
class PL_chessknight extends BasePlugin implements PointsGiver {

    const MAX_TASKS = 4;       //Наксимальное кол-во найденных решений, за которое будут даны очки
    const POINTS_PER_TASK = 5; //Кол-во очков за одно найденное решение

    public function getName() {
        return 'Задача о ходе шахматного коня';
    }

    public function getDescr() {
        return "Реализация легендарной задачи о ходе шахматного коня.\n Вам необходимо обойти всё поле, побывав в каждой из ячеек один раз.";
    }

    public function getPluginContent($content, ArrayAdapter $params, PluginFetchingContext $ctxt) {
        $data['solutions'] = ChessKnightManager::getInstance()->getSystemSolutions();
        return new PluginContent($this->getFoldedEntity()->fetchTpl($data));
    }

    /**
     * Метод выдаёт очки пользователю
     */
    public function givePointsImpl(GivePointsContext $ctxt) {
        $this->doGivePoints($ctxt);
    }

    /**
     * Метод проверяет, не нужно ли пользователю выдать очки
     */
    public function checkPointsImpl(GivePointsContext $ctxt) {
        $this->doGivePoints($ctxt);
    }

    /**
     * Краткое описнаие причины выдачи очков
     */
    public function shortReason(UserPointDO $point) {
        $num = $this->decodeNum($point->getData());
        return "Найдено $num решение задачи о ходе коня";
    }

    /**
     * Полное описание причины выдачи очков
     */
    public function fullReason(UserPointDO $point) {
        $max = self::MAX_TASKS * self::POINTS_PER_TASK;
        $left = $max - $point->getData() * self::POINTS_PER_TASK;
        return "Всего можно заработать $max баллов, осталось: $left";
    }

    /**
     * Имплементация метода выдачи очков пользователям за найденные решения
     */
    private function doGivePoints(GivePointsContext $ctxt) {
        $tillNum = min(ChessKnightBean::inst()->getUserSolvedCnt($ctxt->getUserId()), self::MAX_TASKS);

        for ($index = 1; $index <= $tillNum; $index++) {
            $ctxt->givePoints(self::POINTS_PER_TASK, $index);
        }
    }

    public function decodeNum($num) {
        switch ($num) {
            case 1:
                return 'первое';
            case 2:
                return 'второе';
            case 3:
                return 'третье';
            case 4:
                return 'четвёртое';
            case 5:
                return 'пятое';
            case 6:
                return 'шестое';
        }
        return 'очередное';
    }

}

?>
