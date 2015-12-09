<?php

require_once dirname(dirname(__DIR__)) . '/ajax/AjaxTools.php';

execute_ajax_action(AdminAjaxActions::getAction());
?>