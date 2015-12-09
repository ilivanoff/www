<?php

/**
 * Базовый интерфейс для провайдеров новостей.
 *
 * @author azazello
 */
interface NewsProvider {

    /**
     * Загрузка ограниченного кол-ва событий, начиная с последнего загруженного
     */
    public function getNewsEvents($lastUnique, $limit);

    /**
     * Уникальный тип событий, для дозагрузки
     */
    public function getNewsEventType();

    /**
     * Предзагрузка событий перед получением их графического представления
     */
    public function preloadNewsEvents(array $uniques);

    /**
     * Графическое представление новости
     */
    public function getNewsEventPresentation(NewsEventInterface $event);
}

?>
