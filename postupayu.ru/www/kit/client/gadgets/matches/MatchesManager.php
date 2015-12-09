<?php

/**
 * Менеджер для работы с задачей о раскладке спичек
 */
class MatchesManager extends AbstractSingleton {

    //Задачи, ответы на которые регистрируем
    private $TASKS = array(
        array('m' => 8, 's' => 2, 'c' => 3, 'r' => 3),
        array('m' => 8, 's' => 4, 'c' => 3, 'r' => 3),
        array('m' => 6, 's' => 3, 'c' => 3, 'r' => 3),
        array('m' => 4, 's' => 5, 'c' => 3, 'r' => 3),
        array('m' => 4, 's' => 4, 'c' => 3, 'r' => 3),
        array('m' => 5, 's' => 3, 'c' => 3, 'r' => 3),
        array('m' => 5, 's' => 3, 'c' => 3, 'r' => 2),
        array('m' => 4, 's' => 3, 'c' => 3, 'r' => 2)
    );
    private $IDENT2TASK;

    private function taskByIdent($id) {
        if (!isset($this->IDENT2TASK)) {
            $this->IDENT2TASK = array();
            foreach ($this->TASKS as $task) {
                $this->IDENT2TASK[$this->taskIdent($task)] = $task;
            }
        }
        check_condition(array_key_exists($id, $this->IDENT2TASK), "No task with id: [$id].");
        return $this->IDENT2TASK[$id];
    }

    private function taskIdent(array $task) {
        return $task['m'] . 'x' . $task['s'] . 'x' . $task['c'] . 'x' . $task['r'];
    }

    private function matchId($x0, $y0, $x1, $y1) {
        return $x0 . 'x' . $y0 . 'x' . $x1 . 'x' . $y1;
    }

    private function taskByNum($num) {
        $task = array_key_exists($num, $this->TASKS) ? $this->TASKS[$num] : null;
        check_condition(is_array($task), "Bad matches task num given: [$num].");
        return $task;
    }

    /**
     * Регистрирует решение задачи
     * 
     * @return bool - признак, был ли ответ привязан к авторизованному пользователю
     */
    public function registerSolution($taskIdent, $matchesStr) {
        $task = $this->taskByIdent($taskIdent);
        $matchesStr = normalize_string($matchesStr, true);

        $mCnt = $task['m'];
        $sCnt = $task['s'];
        $cols = $task['c'];
        $rows = $task['r'];

        $excluded = $this->strToArray($matchesStr, $mCnt, $cols, $rows);
        ksort($excluded);

        //Вычисляем оставшиеся спички (пробегаем по всем точкам и берём правую и верхнюю спичку)
        $matches = array();
        for ($x = 0; $x <= $cols; $x++) {
            for ($y = 0; $y <= $rows; $y++) {
                $matchId = $this->matchId($x, $y, $x + 1, $y);
                if (!array_key_exists($matchId, $excluded) && ($x + 1 <= $cols)) {
                    $matches[$matchId] = true;
                }

                $matchId = $this->matchId($x, $y, $x, $y + 1);
                if (!array_key_exists($matchId, $excluded) && ($y + 1 <= $rows)) {
                    $matches[$matchId] = true;
                }
            }
        }

        $sqCnt = 0;
        //Скопируем массив
        $badMatches = $matches;
        //Вычисляем координаты всех квадратов
        for ($x = 0; $x < $cols; $x++) {
            for ($y = 0; $y < $rows; $y++) {
                //Проверяем точку
                $endX = $x;
                $endY = $y;
                while ((++$endX <= $cols) && (++$endY <= $rows)) {
                    $bounds = $this->isFullSquare($x, $y, $endX, $endY, $matches);
                    if ($bounds) {
                        ++$sqCnt;
                        foreach ($bounds as $key => $val) {
                            unset($badMatches[$key]);
                        }
                    }
                }
            }
        }

        $badMatches = empty($badMatches) ? false : concat(array_keys($badMatches));
        check_condition(!$badMatches, "Bad matches left: [$badMatches]");
        check_condition($sqCnt == $sCnt, "Invalid squares cnt: [$sqCnt], required: [$sCnt].");


        //Сохраняем в базу
        $userId = AuthManager::getUserIdOrNull();
        //Склеим строку из отсартированных спичек
        $matchesStr = $this->arrToStr($excluded);
        //Регистрируем ответ пользователя
        $ansBindedToUser = MatchesBean::inst()->registerAnswer($taskIdent, $matchesStr, $userId);
        //Если зарегистрировали, попробуем дать очки
        if ($ansBindedToUser && $userId) {
            PL_matches::inst()->givePoints(PsUser::inst());
        }
        //Возвратим признак выданных очков
        return $ansBindedToUser;
    }

    //В случае, если квадрат - замкнутый, возвращаются граничные спички.
    private function isFullSquare($_x, $_y, $_endX, $_endY, $matches) {
        $boundMatches = array();
        //НИЗ + ВЕРХ
        for ($x = $_x; $x < $_endX; $x++) {
            $matchId = $this->matchId($x, $_y, $x + 1, $_y);
            if (!array_key_exists($matchId, $matches)) {
                return false;
            }
            $boundMatches[$matchId] = true;

            $matchId = $this->matchId($x, $_endY, $x + 1, $_endY);
            if (!array_key_exists($matchId, $matches)) {
                return false;
            }
            $boundMatches[$matchId] = true;
        }

        //ПРАВО + ЛЕВО
        for ($y = $_y; $y < $_endY; $y++) {
            $matchId = $this->matchId($_x, $y, $_x, $y + 1);
            if (!array_key_exists($matchId, $matches)) {
                return false;
            }
            $boundMatches[$matchId] = true;

            $matchId = $this->matchId($_endX, $y, $_endX, $y + 1);
            if (!array_key_exists($matchId, $matches)) {
                return false;
            }
            $boundMatches[$matchId] = true;
        }

        return $boundMatches;
    }

    //Разбивает строку 00011112 на спички 0x0x0x1 и 1x1x1x2
    private function strToArray($matchesStr, $mCnt, $cols, $rows) {
        $len = strlen($matchesStr);
        check_condition($len == $mCnt * 4, "Not [$mCnt] matches in string [$matchesStr]");

        $data = array();

        for ($index = 0; $index < $len; $index += 4) {
            $x0 = (int) $matchesStr[$index];
            $y0 = (int) $matchesStr[$index + 1];
            $x1 = (int) $matchesStr[$index + 2];
            $y1 = (int) $matchesStr[$index + 3];

            $dx = $x1 - $x0;
            $dy = $y1 - $y0;

            check_condition(($dx == 0 && $dy == 1) || ($dx == 1 && $dy == 0), "Bad matches coords [$x0, $y0, $x1, $y1], dx=[$dx], dy=[$dy]");
            check_condition($x0 >= 0 && $x0 <= $cols, "Bad x coord [$x0]");
            check_condition($y0 >= 0 && $y0 <= $rows, "Bad y coord [$y0]");

            $matchId = $this->matchId($x0, $y0, $x1, $y1);
            check_condition(!array_key_exists($matchId, $data), "Match [$matchId] occured twice");
            $data[$matchId] = array($x0, $y0, $x1, $y1);
        }

        return $data;
    }

    //Комбинирует правильные ответы в строку
    private function arrToStr(array $matches) {
        $res = '';
        foreach ($matches as $id => $value) {
            $res .= $value[0] . '' . $value[1] . '' . $value[2] . '' . $value[3];
        }
        return $res;
    }

    /*
     * 
     * 
     * Перевод задач в строку
     * 
     * 
     */

    public function tasksHtml() {
        $res = '';
        foreach ($this->TASKS as $task) {
            $taskIdent = $this->taskIdent($task);
            $task['ident'] = $taskIdent;
            $text = $this->taskToString($task);

            $PARAMS['data'] = $task;
            $PARAMS['class'] = $taskIdent;
            $res .= PsHtml::div($PARAMS, $text);
        }
        return $res;
    }

    //array('m' => 8, 's' => 2, 'c' => 3, 'r' => 3),
    private function taskToString(array $task) {
        $m = $task['m'];
        $mStr = $this->mToStr($m);
        $s = $task['s'];
        $sStr = $this->sToStr($s);
        return "Уберите $m $mStr так, чтобы оставшиеся спички составили $s $sStr.";
    }

    private function mToStr($m) {
        switch ($m) {
            case 1:
                return 'спичку';
            case 2:
            case 3:
            case 4:
                return 'спички';
            default:
                return 'спичек';
        }
    }

    private function sToStr($s) {
        switch ($s) {
            case 1:
                return 'квадрат';
            case 2:
            case 3:
            case 4:
                return 'квадрата';
            default:
                return 'квадратов';
        }
    }

    /*
     * 
     * Работа с базой
     * 
     */

    public function isUserSolweAllTasks($userId) {
        return MatchesBean::inst()->getSolvedTasksCnt($userId) >= count($this->TASKS);
    }

    /**
     *   Пользователь может видеть ответы только к тем задачам, которые решил сам
     */
    public function getAnswers4User() {
        $userId = AuthManager::getUserIdOrNull();
        return $userId ? MatchesBean::inst()->getTasksAnswers4User($userId) : null;
    }

    public function getAnswer4User($taskIdent) {
        $userId = AuthManager::getUserIdOrNull();
        return $userId ? MatchesBean::inst()->getTaskAnswers4User($userId, $taskIdent) : null;
    }

    /** @return MatchesManager */
    public static function getInstance() {
        return self::inst();
    }

}

?>
