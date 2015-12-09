<?php

function smarty_function_varres($params, Smarty_Internal_Template & $smarty) {
    SmartyBlockContext::getInstance()->getParentBlockSetVirtualCtxt(array('task', 'ex'), __FUNCTION__, true);
    $num = SmartyBlockContext::getInstance()->getNumAndIncrease(SmartyBlockContext::ALTERNATE_SOLUTION_NUM);
    SmartyBlockContext::getInstance()->dropVirtualContext();

    switch ($num) {
        case 1:
            $text = 'I. Первый';
            break;
        case 2:
            $text = 'II. Второй';
            break;
        case 3:
            $text = 'III. Третий';
            break;
        case 4:
            $text = 'IV. Четвёртый';
            break;
        case 5:
            $text = 'V. Пятый';
            break;
        case 6:
            $text = 'VI. Шестой';
            break;
        case 7:
            $text = 'VII. Седьмой';
            break;
        case 8:
            $text = 'VIII. Восьмой';
            break;
        case 9:
            $text = 'IX. Девятый';
            break;
        case 10:
            $text = 'X. Десятый';
            break;
        default:
            die("Not enougth var resh nums");
    }

    echo "<p><strong>$text вариант решения:</strong></p>";
}
