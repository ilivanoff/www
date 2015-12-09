<?php

/*
 * Интерфейс для тех контекстов, в рамках которых проходит нумерация формул
 */
interface FormulaNumeratorContext {

    const CSS_NUMERATOR_FORMULA = PsConstJs::CSS_NUMERATOR_FORMULA;

    public function wrapFormulaBox($formulaId, $content);

    public function getFormulaHref($formulaId);
}

?>