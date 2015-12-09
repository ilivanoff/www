<?php

/**
 * Базовый класс для финализаторов построения страницы.
 * Под финализацией понимаются любые действия, которые должны быть выполнены до показа стрыницы пользователю.
 * Все финализаторы пишут в логгер PageBuilder`а и от его имени и работают.
 */
abstract class AbstractPageFinalizer {

    /** @var PsLoggerInterface */
    protected $LOGGER;

    /** @var PsProfilerInterface */
    private $PROFILER;

    /** Счётчик вызовов */
    private static $call = 0;

    protected abstract function doFinalize($CONTENT);

    public static final function finalize(PsLoggerInterface $LOGGER, $CONTENT) {
        //Проверим на пустоту
        if (isEmpty($CONTENT)) {
            return $CONTENT;
        }

        $call = ++self::$call;

        //Создадим экземпляр финализатора и вызовем его
        $class = get_called_class();
        $PROFILER = PsProfiler::inst($class);
        $inst = new $class($LOGGER, $PROFILER);
        $PROFILER->start(__FUNCTION__);
        $SECUNDOMER = Secundomer::startedInst();

        $LOGGER->infoBox(">>> CALLED $call. $class");

        try {
            $CONTENT = $inst->doFinalize($CONTENT);
            $PROFILER->stop();
            $SECUNDOMER->stop();
        } catch (Exception $ex) {
            $PROFILER->stop(false);
            $LOGGER->infoBox("Exception occured while calling $class::finalize. Message: " . $ex->getMessage());
            throw $ex;
        }

        $LOGGER->infoBox("<<< CALL $call. $class FINISHED IN " . $SECUNDOMER->getAverage() . ' seconds');

        return $CONTENT;
    }

    private final function __construct(PsLoggerInterface $LOGGER, PsProfilerInterface $PROFILER) {
        $this->LOGGER = $LOGGER;
        $this->PROFILER = $PROFILER;
    }

}

?>
