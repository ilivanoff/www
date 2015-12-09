<?php

/**
 * Интерфейс для тех контекстов фолдинга, для которых происходит сбор и построение спрайтов
 */
interface SpritableContext {

    /**
     * В нашем случае всё предельно просто - FoldedEntity и есть Spritable.
     * 
     * @return Spritable
     */
    public function getSpritable();
}

?>