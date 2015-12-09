<?php

/**
 * Менеджер для работы с задачей о ходе шахматного коня
 */
class ChessKnightManager extends AbstractSingleton {

    private function hodId($x, $y) {
        return $x . 'x' . $y;
    }

    private function getNextAllowedHodes($x, $y) {
        $allowedHodes = array(
            array($x - 1, $y - 2),
            array($x - 1, $y + 2),
            array($x - 2, $y - 1),
            array($x - 2, $y + 1),
            array($x + 1, $y - 2),
            array($x + 1, $y + 2),
            array($x + 2, $y - 1),
            array($x + 2, $y + 1)
        );

        $result = array();

        foreach ($allowedHodes as $hod) {
            $x = $hod[0];
            $y = $hod[1];

            if ($x > 0 && $x < 9 && $y > 0 && $y < 9) {
                $result[$this->hodId($x, $y)] = true;
            }
        }

        return $result;
    }

    /*
     * Функция отвечает за регистрацию решения задачи о ходе коня
     */

    public function registerSolution(/* array or string */ $hodes) {
        if (is_string($hodes)) {
            $hodes = $this->strToArray($hodes);
        }

        if (!is_array($hodes) || count($hodes) != 64) {
            return false;
        }

        $hodesDone = array();
        $hodesAllowed = null;
        $hodesString = '';
        foreach ($hodes as $hod) {
            $x = $hod[0];
            $y = $hod[1];

            $hodesString.="$x$y";
            $hodId = $this->hodId($x, $y);
            if ($x > 0 && $x < 9 && $y > 0 && $y < 9 && !array_key_exists($hodId, $hodesDone) &&
                    (!$hodesAllowed || array_key_exists($hodId, $hodesAllowed))) {
                $hodesDone[$hodId] = true;
                $hodesAllowed = $this->getNextAllowedHodes($x, $y);
                continue;
            }

            return false;
        }

        //Пользователь может быть и не авторизован
        $userId = AuthManager::getUserIdOrNull();

        $answerBinded = ChessKnightBean::inst()->registerAnswer($hodesString, $userId);

        if ($userId && $answerBinded) {
            //Дадим очки
            PL_chessknight::inst()->givePoints(PsUser::inst());
        }

        return true;
    }

    public function getSystemSolutions() {
        return ChessKnightBean::inst()->getSystemSolutions(AuthManager::getUserIdOrNull());
    }

    private function strToArray($answer) {
        if (strlen($answer) != 128) {
            return null;
        }

        $data = array();

        for ($index = 0; $index < strlen($answer); $index += 2) {
            $data[] = array($answer[$index], $answer[$index + 1]);
        }

        return $data;
    }

    /** @return ChessKnightManager */
    public static function getInstance() {
        return self::inst();
    }

}

?>
