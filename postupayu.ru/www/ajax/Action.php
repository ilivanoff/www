<?php

require_once 'AjaxTools.php';
require_once 'actions/AjaxActions.php';

execute_ajax_action(AjaxActions::getAction());
?>