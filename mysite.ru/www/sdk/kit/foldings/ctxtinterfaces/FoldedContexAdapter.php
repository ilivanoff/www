<?php

/**
 * Базовый класс для всех адаптеров, реализующих функциональность интерфейса контекста фолдинга.
 *
 * @author azazello
 */
class FoldedContexAdapter {

    /** @var FoldedContext */
    protected $ctxt;

    public final function __construct(FoldedContext $ctxt) {
        $this->ctxt = $ctxt;
    }

}

?>
