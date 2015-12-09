<?php
define('FORM_PARAM_PSCAPTURE', 'pscapture');

session_start();

$requestCapture = array_key_exists(FORM_PARAM_PSCAPTURE, $_REQUEST) ? strtoupper($_REQUEST[FORM_PARAM_PSCAPTURE]): false;
$sessionCapture = array_key_exists(FORM_PARAM_PSCAPTURE, $_SESSION) ? strtoupper($_SESSION[FORM_PARAM_PSCAPTURE]): false;

$valid = $requestCapture !== false && $requestCapture === $sessionCapture;
echo $valid ? 'true' : 'false';
?>
