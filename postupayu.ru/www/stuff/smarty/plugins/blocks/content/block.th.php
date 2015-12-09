<?php

/*
  {th}
  {head}
  <p>Условие теоремы</p>
  {/head}
  <p>Доказательство теоремы</p>
  {/th}
 */

function smarty_block_th($params, $content, Smarty_Internal_Template & $smarty) {
    $ctxtParams = SmartyBlockContext::getInstance()->registerBlock($content, __FUNCTION__);

    if (!$content) {
        return; //---
    }

    $thId = trim(value_Array(array('id', 'num'), $params));

    $num = PostFetchingContext::getInstance()->getNextThNum($thId, true);
    $elId = $thId ? PostFetchingContext::getInstance()->getThElId($thId) : null;

    $tpl = PSSmarty::template('common/theorem.tpl');
    $tpl->assign('id', $elId);
    $tpl->assign('num', $num);
    $tpl->assign('c_body', $content);
    $tpl->assign($ctxtParams);
    $tpl->display();
}

?>
