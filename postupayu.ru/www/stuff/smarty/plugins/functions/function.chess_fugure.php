<?php

function smarty_function_chess_fugure($params, Smarty_Internal_Template &$template) {
    // Постановка фигуры на доску
    $x = $params['x'];
    $y = $params['y'];

    $value = '';
    $figure = '';

    $data = array();
    $class = array();

    if ($y == 0 || $y == 9) {
        switch ($x) {
            case 1:
                $value = 'A';
                break;
            case 2:
                $value = 'B';
                break;
            case 3:
                $value = 'C';
                break;
            case 4:
                $value = 'D';
                break;
            case 5:
                $value = 'E';
                break;
            case 6:
                $value = 'F';
                break;
            case 7:
                $value = 'G';
                break;
            case 8:
                $value = 'H';
                break;
        }
    } else {
        if ($x == 0 || $x == 9) {
            $value = $y;
        } else {
            $class[] = (($x + $y) % 2 == 0 ? 'even' : 'odd');

            $figs = $template->getTemplateVars('figures');
            $pos = $x . $y; //Позиция фигуры, например a6
            if (is_array($figs) && array_key_exists($pos, $figs)) {
                $figure = $figs[$pos];
                $value = "<span class=\"$figure\"></span>";
            }
        }
    }

    return PsHtml::html2('td', array('class' => $class), $value);
}

?>
