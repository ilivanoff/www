<?php

/*
 * Блочная формула, которая будет отображена по центру поста.
 * Может быть текстовой: {f}x+y=\(sin(x)\){/f}, или блочной tex формулой: {f}\[\exp(2)\]{/f}.
 */

function smarty_block_f($params, $content, Smarty_Internal_Template & $template) {
    if (isEmpty($content)) {
        return; //---
    }

    /*
     * Если это не TeX формула \[\], то это текстовая формула и её надо обработать.
     */
    if (!TexTools::isBlockFormula($content)) {
        $content = TextFormulesProcessor::processBlockFormula($content);
    }

    /*
     * Проверим, задан ли идентификатор для формулы.
     * Если задан - пронумеруем её, т.к. на неё будут ссылаться.
     */
    $formulaId = trim(value_Array(array('id', 'num'), $params));

    if (!$formulaId) {
        return $content;
    }

    return FoldedContextWatcher::getInstance()->getFormulaNumeratorContext()->wrapFormulaBox($formulaId, $content);
}

?>
