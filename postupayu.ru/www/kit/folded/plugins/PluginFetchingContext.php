<?php

/**
 * Контекст, в котором выполняется фетч плагина
 */
class PluginFetchingContext extends FoldedContext implements TasksNumeratorContext, FormulaNumeratorContext, SpritableContext {

    /** @return PluginFetchingContext */
    public static function getInstance() {
        return parent::inst();
    }

    /** @return FoldedEntity */
    public function getFoldedEntity() {
        return PluginsManager::inst()->getFoldedEntity($this->getContextIdent());
    }

    public function setContextWithFoldedEntity(FoldedEntity $entity) {
        $this->setContext($entity->getIdent());
    }

    /**
     * TasksNumeratorContext
     */
    public function getNextTaskNumber() {
        return $this->TasksNumeratorContext()->getNextTaskNumber();
    }

    public function getTasksCount() {
        return $this->TasksNumeratorContext()->getTasksCount();
    }

    public function resetTasksNumber() {
        $this->TasksNumeratorContext()->resetTasksNumber();
    }

    /**
     * FormulaNumeratorContext
     */
    public function wrapFormulaBox($formulaId, $content) {
        return $this->FormulaNumeratorContext()->wrapFormulaBox($formulaId, $content);
    }

    public function getFormulaHref($formulaId) {
        return $this->FormulaNumeratorContext()->getFormulaHref($formulaId);
    }

    /**
     * SpritableContext
     */
    public function getSpritable() {
        return $this->SpritableContext()->getSpritable();
    }

}

?>