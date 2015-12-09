<?php

require_once 'AjaxTools.php';

$data = RequestArrayAdapter::inst()->arr('programm');

if ($data) {
    $programm = new GymProgramm($data);
    if ($programm->hasExercises()) {
        $programmId = GymManager::getInstance()->saveProgramm($programm);
        json_success($programmId);
    } else {
        json_error('Программа не содержит ни одного упражнения');
    }
} else {
    json_error('Не переданы данные');
}
?>