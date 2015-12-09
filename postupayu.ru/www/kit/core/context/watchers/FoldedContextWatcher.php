<?php

class FoldedContextWatcher extends AbstractContextWatcher {

    /** @return FoldedContextWatcher */
    public static function getInstance() {
        return parent::inst();
    }

    protected function isOurContext(AbstractContext $ctxt) {
        return $ctxt instanceof FoldedContext;
    }

    /** @return FoldedContext */
    public function getContext() {
        return parent::getContext();
    }

    /** @return FoldedEntity */
    public function getFoldedEntity() {
        return $this->hasContext() ? $this->getContext()->getFoldedEntity() : null;
    }

    /**
     * Возвращает FoldedContext, если он имеет заданный тип
     */
    private function getContextImpl($__FUNCTION__, $checkSetted) {
        $context = cut_string_start($__FUNCTION__, 'get');
        check_condition(interface_exists($context), "Interface [$context] is not exists");
        $result = $this->getContext() instanceof $context ? $this->getContext() : null;
        check_condition(!$checkSetted || $result, "$context is not setted now");
        return $result;
    }

    /** @return ImageNumeratorContext */
    public function getImageNumeratorContext($ensure = true) {
        return $this->getContextImpl(__FUNCTION__, $ensure);
    }

    /** @return FormulaNumeratorContext */
    public function getFormulaNumeratorContext($ensure = true) {
        return $this->getContextImpl(__FUNCTION__, $ensure);
    }

    /** @return TasksNumeratorContext */
    public function getTasksNumeratorContext($ensure = true) {
        return $this->getContextImpl(__FUNCTION__, $ensure);
    }

    /** @return SpritableContext */
    public function getSpritableContext($ensure = false) {
        return $this->getContextImpl(__FUNCTION__, $ensure);
    }

    /** @return FoldedEntity */
    public function getFoldedEntityEnsureType($foldingType) {
        $entity = $this->getFoldedEntity();
        check_condition($entity, "Не установлен контекст для определения фолдинга с типом [$foldingType].");
        check_condition($entity->getFolding()->isItByType($foldingType), "Установленный контекст {$this->getContext()} не соответствует фолдингу с типом [$foldingType].");
        return $entity;
    }

    /**
     * Для всех контекстов, которые сохраняют своё состояние, нужно отметить зависимость от сущности фолдинга.
     */
    public function setDependsOnEntity(FoldedEntity $parent) {
        foreach ($this->CTXTS as $ctxt) {
            $ctxt->getFoldedEntity()->setDependsOnEntity($parent);
        }
    }

}

?>