<?php

/**
 * Реализация интерфейса для тех контекстов, в рамках которых проходит нумерация формул
 */
class FormulaNumeratorContextImpl extends FoldedContexAdapter implements FormulaNumeratorContext {

    const FORMULA_ID2NUM = 'FORMULA_ID2NUM';
    const CURRENT_FORMULA_NUM = 'CURRENT_FORMULA_NUM';

    private function getNextFormulaNum($formulaId, $doRegister) {
        $num = $formulaId ? $this->ctxt->getMappedParam(self::FORMULA_ID2NUM, $formulaId) : null;
        if (!is_numeric($num) && $doRegister) {
            $num = $this->ctxt->getNumAndIncrease(self::CURRENT_FORMULA_NUM);
            $this->ctxt->setMappedParam(self::FORMULA_ID2NUM, $formulaId, $num);
        }
        return $num;
    }

    public function getFormulaElId($formulaId) {
        return $this->ctxt->getFoldedEntity()->getUnique(IdHelper::formulaId(trim($formulaId)));
    }

    public function wrapFormulaBox($formulaId, $formula) {
        $formulaId = trim($formulaId);

        $num = $this->getNextFormulaNum($formulaId, true);
        $elId = $this->getFormulaElId($formulaId);

        $tpl = PSSmarty::template('common/formula_jax_block.tpl');
        $tpl->assign('id', $elId);
        $tpl->assign('text', $formula);
        $tpl->assign('index', PsConstJs::numeratorItemIndex(self::CSS_NUMERATOR_FORMULA, $num));
        return $tpl->fetch();
    }

    public function getFormulaHref($formulaId) {
        $formulaId = trim($formulaId);

        $num = $this->getNextFormulaNum($formulaId, false);

        if (!$num) {
            return PsHtml::spanErr("Ссылка на незарегистрированную формулу с идентификатором '$formulaId'");
        }

        $boxId = $this->getFormulaElId($formulaId);

        return PsBubble::spanById($boxId, '(' . PsConstJs::numeratorHrefIndex(self::CSS_NUMERATOR_FORMULA, $num) . ')', 'formula');
    }

}

?>