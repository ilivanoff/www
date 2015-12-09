<?php

/*
 * Работа с ребусами.
 * Всё идёт по следующему сценарию:
 * Рядом с классом PsMathRebus лежит файл с ребусами и ответами на них: kit...rebuses.txt
 * Процесс берёт файл rebuses.txt с ребусами и обрабатывает его, учитывая, какие ребусы уже были обработаны.
 * По факту обработки строится файл с ответами и копируется на место kit...rebuses.txt
 */

require_once '../ToolsResources.php';
$CALLED_FILE = __FILE__;

$LOGGERS_LIST[] = 'PsMathRebusSolver';

$rebuses = DirItem::inst(__DIR__, 'rebuses.txt')->getTextFileAdapter();
$MR = PsMathRebus::inst();

$result = array();

foreach ($rebuses->getLines() as $rebus) {
    if (starts_with($rebus, '#')) {
        continue;
    }
    $rebus = $MR->normalize($rebus);
    switch ($MR->rebusState($rebus)) {
        case PsMathRebus::STATE_HAS_ANSWERS:
            dolog("Take rebus answers: $rebus");
            $result[$rebus] = $MR->rebusAnswers($rebus);
            break;
        case PsMathRebus::STATE_NO_ANSWERS:
            dolog("Scipping rebus: $rebus");
            $result[$rebus] = array();
            break;
        case PsMathRebus::STATE_NOT_REGISTERED:
            dolog("Processing rebus: $rebus");
            $result[$rebus] = PsMathRebusSolver::solve($rebus);
            break;
    }
}

$ansDI = DirItem::inst(__DIR__, 'answers.txt');
$ansDI->remove();
foreach ($result as $rebus => $answers) {
    $ansDI->writeLineToFile($rebus);
    foreach ($answers as $answer) {
        $ansDI->writeLineToFile($answer);
    }
    $ansDI->writeLineToFile();
}

if (getCmdParam(1) == 1) {
    dolog('Copy from [' . $ansDI->getRelPath() . '] to [' . $MR->getAnswersDI()->getRelPath() . ']');
    $ansDI->copyTo($MR->getAnswersDI()->getAbsPath());
}
?>
