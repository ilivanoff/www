<?php

//1523
class UP_8monets extends AbstractPointsGiver implements PointsGiverRequest {

    public function givePointsByRequest(GivePointsContext $ctxt, ArrayAdapter $request) {
        $this->givePointsImpl($ctxt, $request->str('hodes'));
    }

    public function givePointsImpl(GivePointsContext $ctxt, $hodesStr = null) {
        if (!is_string($hodesStr) || !is_numeric($hodesStr) || strlen($hodesStr) != 4 || in_array($hodesStr, array('5123', '1352'))) {
            return; //---
        }

        $hodesArr[] = (int) $hodesStr[0];
        $hodesArr[] = (int) $hodesStr[1];
        $hodesArr[] = (int) $hodesStr[2];
        $hodesArr[] = (int) $hodesStr[3];

        if ($this->isValid($hodesArr)) {
            $ctxt->givePoints(2, $hodesStr);
        }
    }

    public function checkPointsImpl(GivePointsContext $ctxt) {
        //---
    }

    public function shortReason(UserPointDO $point) {
        return 'Решена задача с 8ю монетами, порядок: ' . $point->getData();
    }

    public function fullReason(UserPointDO $point) {
        return '';
    }

    //Проверка валидности решения
    private function isValid($hodes) {
        $monets = array();
        for ($index = 1; $index <= 8; $index++) {
            $monets[$index] = 1;
        }

        $from;
        $to;
        foreach ($hodes as $hod) {
            if ($hod < 1 || $hod > 5) {
                return false;
            }

            if ($monets[$hod] != 1) {
                return false;
            }

            $monets[$hod] = 0;
            $from = $hod;

            $cursor = 0;
            do {
                ++$hod;
                if ($monets[$hod] <> 0) {
                    ++$cursor;
                }

                if ($cursor == 3 && $hod <= 8 && $monets[$hod] == 1) {
                    $monets[$hod] = 2;
                    $to = $hod;
                    //echo "$from->$to ";
                    break;
                }
            } while ($hod < 8);

            if ($cursor != 3) {
                return false;
            }
        }

        return !in_array(1, $monets);
    }

}

?>