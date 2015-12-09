<?php

/**
 * Интерфейс, реализуемый всеми классами фолдинга, которые претендуют на то,
 * чтобы раздавать очки пользователям.
 *
 * @author azazello
 */
interface PointsGiverMaster {

    /**
     * Метод, вызываемый для выдачи очков пользователю.
     */
    public function givePoints(PsUser $user, $param1 = null, $param2 = null);

    /**
     * Метод, вызываемый для проверки, нет ли у пользователя очков, положенных, 
     * но не выданных ему.
     */
    public function checkPoints(PsUser $user);
}

?>
