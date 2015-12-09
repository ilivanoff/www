<?php

/**
 * 
 * Базовый интерфейс, который должны наследовать все классы, которые могут отображать 
 * свою информацию в новостной ленте (новостные события).
 *
 * @author azazello
 */
interface NewsEventInterface {

    /**
     * Дата события в формате utc
     */
    public function getNewsEventUtc();

    /**
     * Уникальный код события в рамках данного типа событий
     */
    public function getNewsEventUnique();
}

?>
