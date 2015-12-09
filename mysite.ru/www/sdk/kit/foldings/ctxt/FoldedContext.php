<?php

/**
 * Базовый контекст, в рамках которого происходит фетчинг шаблонов фолдингов.
 * Фетч шаблонов обязательно должен проходить в контексте сущности фолдинга
 * (для работы различных смарти функций и т.д.).
 *
 * @author azazello
 */
class FoldedContext extends AbstractContext {

    public function tplFetchParamsClass() {
        return FoldedTplFetchPrams::getClassName();
    }

    /**
     * Функция, которая будет вызвана после фетчинга шаблона для оборачивания
     * содержимого в специальный элемент.
     */
    protected function wrapContent($content, array $params = array()) {
        $params['id'] = $this->getFoldedEntity()->getUnique();
        $params['class'][] = PsUtil::getClassConsts(get_called_class(), 'CSS_');
        return PsHtml::div($params, $content);
    }

    /**
     * Метод вызывается после фетчинга шаблона для финализации работы - добавления 
     * нужных параметров из контекста и т.д.
     */
    public function finalizeTplContent($content) {
        $PARAMS[FoldedTplFetchPrams::PARAM_CONTENT] = $this->wrapContent($content);
        return $PARAMS;
    }

    /** @return FoldedEntity */
    public function getFoldedEntity() {
        return $this->getContext();
    }

    public function setContextWithFoldedEntity(FoldedEntity $entity) {
        $this->setContext($entity->getUnique(), $entity);
    }

    /** @return FoldedContext */
    public static function getInstance() {
        return parent::inst();
    }

    /**
     * Различные адаптеры для мгновенного подключения функциональности - нумерация картинок, формул и т.д.
     */
    private $ADAPTERS = array();

    private function getAdapterImpl($interface) {
        if (array_key_exists($interface, $this->ADAPTERS)) {
            return $this->ADAPTERS[$interface];
        }

        check_condition(interface_exists($interface), "Cannot create $interface adapter, interface not exists.");
        check_condition($this instanceof $interface, 'Context ' . get_called_class() . " is not subclass of $interface, cannot use adapter.");

        $class = $interface . 'Impl';

        check_condition(class_exists($class), "Cannot create $class adapter, class not exists.");
        //check_condition(class_implements($class, $interface), "Class $class is not subclass of $interface, cannot create adapter.");
        check_condition(is_subclass_of($class, 'FoldedContexAdapter'), "Class $class is not subclass of FoldedContexAdapter, cannot create adapter.");

        return $this->ADAPTERS[$interface] = new $class($this);
    }

    /** @return ImageNumeratorContext */
    protected function ImageNumeratorContext() {
        return $this->getAdapterImpl(__FUNCTION__);
    }

    /** @return TasksNumeratorContext */
    protected function TasksNumeratorContext() {
        return $this->getAdapterImpl(__FUNCTION__);
    }

    /** @return FormulaNumeratorContext */
    protected function FormulaNumeratorContext() {
        return $this->getAdapterImpl(__FUNCTION__);
    }

    /** @return SpritableContext */
    protected function SpritableContext() {
        return $this->getAdapterImpl(__FUNCTION__);
    }

}

?>