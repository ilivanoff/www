<?php

/*
 * Ссылки на формулы в постах.
 */

function smarty_modifier_fhref($formulaId) {
    return FoldedContextWatcher::getInstance()->getFormulaNumeratorContext()->getFormulaHref($formulaId);
}

?>