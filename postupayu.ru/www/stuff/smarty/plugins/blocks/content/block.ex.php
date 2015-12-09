<?php

/*
  {ex}
  {head}
  <p>Условие примера</p>
  {/head}
  <p>Решение примера</p>
  {ans}
  <p>Ответ примера</p>
  {/ans}
  {/ex}
 */

function smarty_block_ex($params, $content, Smarty_Internal_Template &$template) {
    $ctxtParams = SmartyBlockContext::getInstance()->registerBlock($content, __FUNCTION__);

    if (!$content) {
        return; //---
    }

    $ctxt = PostFetchingContext::getInstance();

    if (array_key_exists('reset', $params)) {
        $ctxt->resetExamplesNum();
    }

    $exId = trim(value_Array(array('id', 'num'), $params));

    $num = $ctxt->getExampleNum($exId, true);
    $elId = $exId ? $ctxt->getExampleElId($exId) : null;

    /* @var $exampleTpl Smarty_Internal_Template */
    $exampleTpl = PSSmarty::template('common/example.tpl');
    $exampleTpl->assign('id', $elId);
    $exampleTpl->assign('num', $num);
    $exampleTpl->assign('c_body', $content);
    $exampleTpl->assign($ctxtParams);
    $exampleTpl->display();
}

?>
