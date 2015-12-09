<?php

function smarty_function_chis_rebus($params, Smarty_Internal_Template & $smarty) {
    $params = ArrayAdapter::inst($params);

    /*
     * Текст ребуса
     */

    $rebus = $params->str('text');
    if ($rebus) {
        return "<div class=\"ps-math-rebus-holder\">$rebus</div>";
    }

    $rebus = $params->str('ans');
    if ($rebus) {
        $answers = PsMathRebus::inst()->rebusAnswers($rebus);
        if (is_array($answers)) {
            foreach ($answers as $ans) {
                echo "<div class=\"ps-math-rebus-holder\">$ans</div> ";
            }
        } else {
            return PsHtml::spanErr($answers);
        }
    }
}
?>

