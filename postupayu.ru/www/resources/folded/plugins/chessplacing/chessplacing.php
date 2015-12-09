<?php

/**
 * Плагин - задача о расстановке фигур на шахматной доске (ладья, король, слон, конь).
 */
class PL_chessplacing extends BasePlugin implements PointsGiver, PointsGiverRequest {

    public function getName() {
        return 'Расстановке фигур на шахматной доске';
    }

    public function getDescr() {
        return 'Реализация задачи о расстановке шахматных фигур на доске так, чтобы они не били друг друга (независимость), либо держали под ударом всё поле (доминирование).';
    }

    public function getPluginContent($content, ArrayAdapter $params, PluginFetchingContext $ctxt) {
        return new PluginContent($this->getFoldedEntity()->fetchTpl());
    }

    /*
     * ОЧКИ
     */

    public function givePointsByRequest(GivePointsContext $ctxt, ArrayAdapter $request) {
        $this->givePointsImpl($ctxt, $request->arr('hodes'));
    }

    public function givePointsImpl(GivePointsContext $ctxt, $modeHodes = null) {
        if ($ctxt->hasPoints()) {
            return; //---
        }

        $check = array(
            //Неваз
            'm0R' => 8,
            'm0K' => 16,
            'm0B' => 14,
            'm0N' => 32,
            'm0Q' => 8,
            //Домин
            'm1R' => 8,
            'm1K' => 9,
            'm1B' => 8,
            'm1N' => 12,
            'm1Q' => 5
        );

        $valid = false;
        if (is_array($modeHodes) && count($modeHodes) == 2) {
            foreach ($modeHodes as $mode => $hodes) {
                foreach ($hodes as $fig => $hod) {
                    $key = $mode . $fig;
                    $valid = array_key_exists($key, $check) && is_numeric($check[$key]) && (strlen($hod) == $check[$key] * 2);
                    if (!$valid) {
                        return; //Невалидный ответ ---
                    }
                }
            }
        }

        if ($valid) {
            $ctxt->givePoints(10);
        }
    }

    public function checkPointsImpl(GivePointsContext $ctxt) {
        
    }

    public function shortReason(UserPointDO $point) {
        return "Решена задача о независимости/доминировании на шашматной доске";
    }

    public function fullReason(UserPointDO $point) {
        return '';
    }

}

?>