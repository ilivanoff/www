<?php

/*
 * Метод должен работать максимально быстро.
 * Сохраняем в сессию параметры перезагрузки страницы, чтобы они могли быть использованы 
 * при построении страницы.
 */

if (count($_REQUEST) == 0) {
    return; //---
}

session_start();

foreach ($_REQUEST as $key => $value) {
    $_SESSION['SESSION_UNLOAD_PARAMS'][$key] = $value;
}
?>