<?php

/**
 * Основной класс, расширяющий функциональность NewsEventInterface посредством 
 * совмещения в себе самого события и провайдера информации о нём.
 * 
 *
 * @author azazello
 */
class NewsEvent implements NewsEventInterface {

    /** @var NewsProvider */
    private $provider;

    /** @var NewsEventInterface */
    private $event;

    function __construct(NewsProvider $provider, NewsEventInterface $event) {
        $this->provider = $provider;
        $this->event = $event;
    }

    /**
     * Дата блока новостей, к которому относится данная новость (16 февраля 2013г.)
     */
    public function getBlockDate() {
        return DatesTools::inst()->uts2dateInCurTZ($this->event->getNewsEventUtc(), DF_NEWS);
    }

    /**
     * html-представление новости для данного события
     */
    public function getPresentation() {
        return $this->provider->getNewsEventPresentation($this->event);
    }

    /**
     * Тип новости
     */
    public function getNewsType() {
        return $this->provider->getNewsEventType();
    }

    public function getNewsEventUtc() {
        return $this->event->getNewsEventUtc();
    }

    public function getNewsEventUnique() {
        return $this->event->getNewsEventUnique();
    }

}

?>