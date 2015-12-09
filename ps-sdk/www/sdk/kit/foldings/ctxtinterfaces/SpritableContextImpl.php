<?php

/**
 * Реализация интерфейса для тех контекстов фолдинга, для которых происходит сбор и построение спрайтов.
 */
class SpritableContextImpl extends FoldedContexAdapter implements SpritableContext {

    /**
     * В нашем случае всё предельно просто - FoldedEntity и есть Spritable.
     * 
     * @return Spritable
     */
    public function getSpritable() {
        return $this->ctxt->getFoldedEntity();
    }

}

?>