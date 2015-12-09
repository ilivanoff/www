<?php

require_once 'AjaxTools.php';

$programmId = RequestArrayAdapter::inst()->int('id');

$errorMsg = null;

if (is_numeric($programmId)) {
    GymManager::getInstance()->deleteProgramm($programmId);
} else {
    $errorMsg = 'Не передан id программы';
}

if ($errorMsg) {
    json_error($errorMsg);
} else {
    json_success('success');
}
?>
