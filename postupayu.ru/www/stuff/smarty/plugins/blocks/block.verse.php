<?php

function smarty_block_verse($params, $content, Smarty_Internal_Template &$template) {
    if (!$content) {
        return;
    }

    $params = ArrayAdapter::inst($params);

    $content = trim($content);

    $name = $params->str('name');
    $year = $params->str('year');
    $double = $params->bool('double');

    /*
     * Обработка содержимого
     */
    $verses = array();
    if ($double) {
        $versesTmp = array();
        $strings = explode("\n", $content);

        $verse = '';
        $break = 0;
        foreach ($strings as $str) {
            $str = trim($str);

            if (!$str) {
                if ($verse) {
                    $verse .= "\n";
                    ++$break;
                }
            } else {
                if ($break > 1) {
                    $versesTmp[] = $verse;
                    $verse = $str;
                } else {
                    $verse .= "\n$str";
                }
                $break = 0;
            }
        }

        if ($verse) {
            $versesTmp[] = $verse;
        }

        $double = count($versesTmp) > 1;

        if ($double) {
            for ($index = 0; $index < count($versesTmp); $index = $index + 2) {
                $v1 = nl2br(trim($versesTmp[$index]));
                $v2 = array_key_exists($index + 1, $versesTmp) ? nl2br(trim($versesTmp[$index + 1])) : '';
                $verses[] = array($v1, $v2);
            }
        }
    }

    /* @var $verseTpl Smarty_Internal_Template */
    $verseTpl = $template->smarty->createTemplate('common/verse.tpl');
    $verseTpl->assign('b_double', $double);
    $verseTpl->assign('c_name', $name/* $name ? $name : '***' */);
    $verseTpl->assign('c_body', nl2br($content));
    $verseTpl->assign('verses', $verses);
    $verseTpl->assign('c_year', $year);
    $verseTpl->display();
}

?>
