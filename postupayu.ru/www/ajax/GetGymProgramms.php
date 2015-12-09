<?php

require_once 'AjaxTools.php';

json_success(GymManager::getInstance()->getProgrammsAsArrays());
?>
