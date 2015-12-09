<?php

$type = array_key_exists('type', $_POST) ? $_POST['type'] : null;
$marker = array_key_exists('marker', $_POST) ? $_POST['marker'] : null;

if (!$type) {
    die('Bad type given.');
}

//MD5_STR_LENGTH ещё использовать нельзя, так как Defines не подключен
if (!$marker || (strlen($marker) <= 32)) {
    die('Bad marker given.');
}

$sessionId = substr($marker, 32);

session_id($sessionId);

require_once 'AjaxTools.php';

check_user_session_marker($marker);
try {
    FileUploader::inst($type)->assertAutonomous();
    FileUploader::inst($type)->saveUploadedFile(true, null, $_POST);
} catch (Exception $ex) {
    PsLogger::inst('AjaxFileUpload')->info('Ошибка загрузки файла');
    PsLogger::inst('AjaxFileUpload')->info($ex->getTraceAsString());
    ExceptionHandler::dumpError($ex);
}
?>