<?php

/*
 * Интерфейс, который должен реализовывать класс, закрытие которого произойдёт по окончанию работы.
 */

interface Destructable {

    function onDestruct();
}

?>