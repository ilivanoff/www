<?php

/**
 * Базовый интерфейс для всех классов фолдинга, которые умеют раздвавать очки при запросе 
 * ajax {@link GivePointsCommon}.
 *
 * @author azazello
 */
interface PointsGiverRequest {

    /**
     * Метод, вызываемый для выдачи очков пользователю после запроса ajax.
     */
    public function givePointsByRequest(GivePointsContext $ctxt, ArrayAdapter $request);
}

?>