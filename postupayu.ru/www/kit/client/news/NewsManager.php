<?php

class NewsManager extends AbstractSingleton {

    /**
     * Загрузка ленты новостей.
     * На вход принимаются последние загруженные состояния, новости будут загружаться после них.
     * Если $initStates = array('tr'=>7, 'is'=>2), то будут выведены tr6, tr5, is1.
     */
    public function getNewsLine(array $initStates = array(), $limit = NEWS_IN_LINE) {
        $EVENTS = array();

        //Соберём все новости и сделаем из коллекции NewsEventInterface коллекцию NewsEvent.
        /* @var $np NewsProvider */
        foreach (Handlers::getInstance()->getNewsProviders() as $np) {
            $type = $np->getNewsEventType();
            /** @var NewsEventInterface */
            foreach ($np->getNewsEvents(array_get_value($type, $initStates), $limit) as $event) {
                $EVENTS[] = new NewsEvent($np, $event);
            }
        }

        //Отсортируем отобранные новости по дате
        usort($EVENTS, function(NewsEvent $e1, NewsEvent $e2) {
                    return $e1->getNewsEventUtc() > $e2->getNewsEventUtc() ? -1 : 1;
                });

        $blocks = array();  //Новостные блоки
        $preload = array(); //Коды отобранных новостей для предзагрузки

        $added = 0;
        $hasMore = false;
        /* @var $event NewsEvent */
        foreach ($EVENTS as $event) {
            $blockDate = $event->getBlockDate();

            /*
             * Прекращаем набор новостных блоков, если:
             * 1. Уже извлечён как минимум один блок
             * 2. Новая новость не входит в этот блок
             * 3. Извлечён лимит новостей
             */
            $hasBlock = array_key_exists($blockDate, $blocks);
            if ($limit && !empty($blocks) && !$hasBlock && $added >= $limit) {
                $hasMore = true;
                break;
            }
            ++$added;

            //Определим блок для новости и поместим её туда
            if (!$hasBlock) {
                $blocks[$blockDate] = new NewsBlock();
            }
            $blocks[$blockDate]->addEvent($event);

            //Отложим событие для предзагрузки
            $preload[$event->getNewsType()][] = $event->getNewsEventUnique();

            //Переустановим последнее состояние
            $initStates[$event->getNewsType()] = $event->getNewsEventUnique();
        }

        //Если хоть один из новостных блоков не закеширован, предзагрузим содержимое всех новостных событий.
        /** @var NewsBlock */
        foreach ($blocks as $block) {
            if (!$block->isCached()) {
                //Предзагрузим содержимое всех событий, которые будут показаны (например - загрузим содержимое всех показываемых постов)
                foreach ($preload as $newsType => $eventsUniques) {
                    Handlers::getInstance()->getNewsProviderByNewsType($newsType)->preloadNewsEvents($eventsUniques);
                }
                break;
            }
        }

        //Осталось собрать новостную ленту из блоков.
        $line = '';
        /** @var NewsBlock */
        foreach ($blocks as $block) {
            $line .= $block->getBlockHtml();
        }

        return array(
            'line' => $line,
            'states' => $initStates,
            'has_more' => $hasMore
        );
    }

    /** @return NewsManager */
    public static function getInstance() {
        return self::inst();
    }

}

?>