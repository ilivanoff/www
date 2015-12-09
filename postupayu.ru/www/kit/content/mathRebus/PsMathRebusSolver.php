<?php

class PsMathRebusSolver {

    /** @var PsLoggerInterface */
    private $LOGGER;
    private $EN = 'abcdefghijklmnopqrstuxyvwz';
    private $RU = 'абвгдеёжзийклмнопрстуфхцчшщъыьэюя';
    /*
     * Кол-во проверенных комбинаций
     */
    private $cnt = 0;

    /*
     * Подготовленный ребус, в котором все символы - английские
     */
    private $REBUS;
    /*
     * Тот-же ребус, в котором '=' заменён на '-'
     */
    private $EXPR;

    /*
     * Комбинации
     */
    private $COMBINATIONS;

    /*
     * Первые символы ребуса (чтобы пропускать для них 0)
     */
    private $FIRST_CHARS = array();

    /*
     * Пропускаем ли первые символы
     */
    private $SCIP_FIRST_CHARS = true;

    private function isScip($num, $char) {
        return $this->SCIP_FIRST_CHARS && $num == 0 && in_array($char, $this->FIRST_CHARS);
    }

    private function isEnChar($char) {
        return contains_substring($this->EN, $char);
    }

    private function isRuChar($char) {
        return contains_substring($this->RU, $char);
    }

    private function variantsCnt($charsCnt) {
        $num = 10;
        $res = 1;
        for ($i = 0; $i < $charsCnt; $i++) {
            $res*=$num--;
        }
        return $res;
    }

    private function doSolve($rebus) {
        $this->LOGGER->info("Обрабатываем ребус: [$rebus], пропускать первые символы={$this->SCIP_FIRST_CHARS}");

        $this->COMBINATIONS = array();

        check_condition($rebus, "Пустой ребус");
        $eqCnt = substr_count($rebus, '=');
        check_condition($eqCnt == 1, "Знак равенства '=' встретился $eqCnt раз");

        $this->LOGGER->info("Приведённый вид: [$rebus]");

        $enCh = array();
        $ruCh = array();
        for ($i = 0; $i < ps_strlen($rebus); $i++) {
            //русский символ
            $letter = ps_charat($rebus, $i);
            if ($this->isRuChar($letter)) {
                if (!array_key_exists($letter, $ruCh)) {
                    $ruCh[$letter] = null;
                }
                continue;
            }
            if ($this->isEnChar($letter)) {
                //английский символ
                if (!in_array($letter, $enCh)) {
                    $enCh[] = $letter;
                }
                continue;
            }
        }

        $this->LOGGER->info('Русские символы: ' . print_r($ruCh, true));
        $this->LOGGER->info('Английские символы: ' . print_r($enCh, true));

        foreach ($ruCh as $ch => $value) {
            for ($i = 0; $i < strlen($this->EN); $i++) {
                $letter = substr($this->EN, $i, 1);
                if (!in_array($letter, $enCh)) {
                    $enCh[] = $letter;
                    $ruCh[$ch] = $letter;
                    break;
                }
            }
        }

        $this->LOGGER->info('После привязки: ');
        $this->LOGGER->info('Русские символы: ' . print_r($ruCh, true));
        $this->LOGGER->info('Английские символы: ' . print_r($enCh, true));

        $enCharsCnt = count($enCh);
        check_condition($enCharsCnt > 0, 'Нет символов для перебора');
        check_condition($enCharsCnt <= 10, "Слишком много переменных: $enCharsCnt");

        $rebus = PsStrings::replaceMap($rebus, $ruCh);
        $this->LOGGER->info("Подготовленный ребус: [$rebus]");
        $this->LOGGER->info("Всего символов для перебора: $enCharsCnt");
        $this->LOGGER->info('Возможных комбинаций: ' . $this->variantsCnt($enCharsCnt));

        $this->REBUS = $rebus;
        $this->EXPR = str_replace('=', '-', $rebus);

        $hasBefore = false;
        for ($i = 0; $i < strlen($rebus); $i++) {
            $char = substr($rebus, $i, 1);
            if ($this->isEnChar($char)) {
                //Символ перебора
                if (!$hasBefore) {
                    $this->FIRST_CHARS[] = $char;
                    $hasBefore = true;
                }
            } else {
                $hasBefore = false;
            }
        }

        $this->LOGGER->info('Начинаем перебор...');

        $numbers = array(
            0 => false,
            1 => false,
            2 => false,
            3 => false,
            4 => false,
            5 => false,
            6 => false,
            7 => false,
            8 => false,
            9 => false);

        $secundomer = Secundomer::startedInst();
        for ($i = 0; $i <= 9; $i++) {
            $letter = reset($enCh);

            if ($this->isScip($i, $letter)) {
                continue;
            }

            $next = next($enCh);
            $numbers[$i] = $letter;
            $this->doCheckIteration($enCh, $next, $numbers);
            $numbers[$i] = false;
        }
        $secundomer->stop();

        $parsed = DatesTools::inst()->parseSeconds(round($secundomer->getTotalTime()));
        $min = $parsed['mf'];
        $sec = pad_zero_left($parsed['s'], 2);
        $parsed = "$min:$sec";


        $combCnt = count($this->COMBINATIONS);
        $this->LOGGER->info("Перебор закончен. Обработано операций: {$this->cnt}, найдено решений: $combCnt.");
        $this->LOGGER->info("Общее время обработки: $parsed.");

        return $this->COMBINATIONS;
    }

    function doCheckIteration(&$letters, $letter, &$numbers) {
        if ($letter !== false) {
            $next = next($letters);
            for ($i = 0; $i <= 9; $i++) {
                if ($this->isScip($i, $letter)) {
                    continue;
                }
                if ($numbers[$i] !== false) {
                    continue;
                }
                $numbers[$i] = $letter;
                $this->doCheckIteration($letters, $next, $numbers);
                $numbers[$i] = false;
            }
            if ($next === false) {
                end($letters);
            } else {
                prev($letters);
            }
        } else {
            $char2num = array();
            foreach ($numbers as $num => $char) {
                if ($char !== false) {
                    $char2num[$char] = $num;
                }
            }
            $this->checkCombination($char2num);
        }
    }

    private function checkCombination(array $char2num) {
        $expr = PsStrings::replaceMap($this->EXPR, $char2num);
        $res = PsMathEvaluator::inst()->e($expr);

        if ($res === false) {
            $error = 'Ошибка обработки ребуса: ' . PsMathEvaluator::inst()->lastError();
            $this->LOGGER->info($error);
            raise_error($error);
        }

        if ($res == 0) {
            $combination = PsStrings::replaceMap($this->REBUS, $char2num);
            $this->COMBINATIONS[] = $combination;
            $this->LOGGER->info($combination);
        }
        ++$this->cnt;
    }

    /*
     * Синглтон, загрузка ребусов
     */

    /** @return PsMathRebus */
    public static function solve($rebus, $skipFirstChars = true) {
        $rebus = PsMathRebus::inst()->normalize($rebus);
        $solver = new PsMathRebusSolver();
        $solver->SCIP_FIRST_CHARS = $skipFirstChars;
        return $solver->doSolve($rebus);
    }

    private function __construct() {
        $this->LOGGER = PsLogger::inst(__CLASS__);
    }

}

?>
