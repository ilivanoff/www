<?php

/*
 * Данный файл подключают те скрипты, которые заведомо должны быть выполнены с ресурсами админа.
 * Это не означает, что для них не будет проверен доступ, но самими классами они пользоваться смогут. 
 */

include_once 'MainImport.php';

ps_admin_on(true);
?>