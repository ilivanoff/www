<?php

/**
 * Базовый класс для всех наблюдателей за сменой контекста.
 */
abstract class AbstractContextWatcher extends AbstractSingleton {

    /** @var PsLoggerInterface */
    protected $LOGGER;

    /* Список контекстов */
    protected $CTXTS = array();

    protected abstract function isOurContext(AbstractContext $ctxt);

    final public function ctxtAction(AbstractContext $ctxt, $isSetted) {
        if (!$this->isOurContext($ctxt)) {
            return; //---
        }

        if ($isSetted) {
            $this->CTXTS[] = $ctxt;
        } else {
            check_condition($this->CTXTS, 'Попытка сбросить контекст, который не был стартован');
            $lastCtxt = array_pop($this->CTXTS);
            check_condition($lastCtxt === $ctxt, 'Сбрасываемый контекст не соответствует установленному: ' . get_class($lastCtxt) . '-' . get_class($ctxt));
        }

        $this->LOGGER->info("{} context: {} - {}", $isSetted ? '+' : '-', get_class($ctxt), $ctxt->getContextIdent());
    }

    final public function hasContext() {
        return !empty($this->CTXTS);
    }

    /** @return AbstractContext */
    public function getContext() {
        return $this->hasContext() ? $this->CTXTS[count($this->CTXTS) - 1] : null;
    }

    final protected function __construct() {
        $this->LOGGER = PsLogger::inst(get_called_class());
    }

}

?>