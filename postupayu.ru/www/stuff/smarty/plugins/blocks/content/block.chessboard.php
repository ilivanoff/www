<?php

/*
  {chessboard}
  bra8,bnb8,bbc8,bqd8,bke8,bbf8,bng8,brh8,
  bpa7,bpb7,bpc7,bpd7,bpe7,bpf7,bpg7,bph7,
  wpa2,wpb2,wpc2,wpd2,wpe2,wpf2,wpg2,wph2,
  wra1,wnb1,wbc1,wqd1,wke1,wbf1,wng1,wrh1,
  {/chessboard}
 */

function smarty_block_chessboard($params, $content, Smarty_Internal_Template &$template) {
    if (!$content) {
        return;
    }

    $content = normalize_string($content, true);
    $content = strtolower($content);
    $arr = explode(',', $content);

    $sets = array();
    foreach ($arr as $value) {
        if (strlen($value) >= 4) {
            $fig = substr($value, 0, 2);
            $pos = substr($value, 2, 2);

            $sets[$pos] = $fig;

            $colNum = false;
            $rowNum = $pos[1];
            switch ($pos[0]) {
                case 'a':
                    $colNum = 1;
                    break;
                case 'b':
                    $colNum = 2;
                    break;
                case 'c':
                    $colNum = 3;
                    break;
                case 'd':
                    $colNum = 4;
                    break;
                case 'e':
                    $colNum = 5;
                    break;
                case 'f':
                    $colNum = 6;
                    break;
                case 'g':
                    $colNum = 7;
                    break;
                case 'h':
                    $colNum = 8;
                    break;
                default:
                    return PsHtml::spanErr("Bad chess board position: [$value]");
            }

            if ($colNum) {
                $sets[$colNum . $rowNum] = $fig;
            }
        }
    }


    /* @var $chessBoardTpl Smarty_Internal_Template */
    $chessBoardTpl = $template->smarty->createTemplate('common/chessboard.tpl');
    $chessBoardTpl->assign('figures', $sets);
    $chessBoardTpl->assign('small', array_key_exists('small', $params));
    $chessBoardTpl->display();
}

?>
