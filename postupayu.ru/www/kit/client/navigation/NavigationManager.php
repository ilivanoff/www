<?php

class NavigationManager extends AbstractSingleton {

    /** @var PsLoggerInterface */
    private $LOGGER;

    /**
     * Структура сайта (навигация и т.д.)
     * 
     * @return NavigationItem
     */
    private function getStructureImpl() {
        $MAP = NavigationItem::byPageCode(BASE_PAGE_MAP);

        $INDEX = NavigationItem::byPageCode(BASE_PAGE_INDEX, 'О проекте');
        $INDEX->addChild(NavigationItem::byPageCode(PAGE_OFFICE));
        $INDEX->addChild(NavigationItem::byPageCode(PAGE_REGISTRATION));
        $INDEX->addChild(NavigationItem::byPageCode(PAGE_PASS_REMIND));

        $MAGAZINE = NavigationItem::byPostsProcessor(MagManager::inst(), 'Все выпуски');

        $BLOG = NavigationItem::byRubricProcessor(BlogManager::inst(), 'Все заметки', 'Все заметки раздела');

        $TRAININGS = NavigationItem::byRubricProcessor(TrainManager::inst(), 'Все занятия', 'Все занятия в разделе');
        $TRAININGS->addChild(NavigationItem::byPageCode(PAGE_LESSON_HOW_TO));

        $FEEDBACK = NavigationItem::byPageCode(BASE_PAGE_FEEDBACK, 'Об авторе');
        $FEEDBACK->addChild(NavigationItem::byHref(FeedbackManager::inst()->writeToUsHref())->setNoBg());
        $FEEDBACK->addChild(NavigationItem::byPageCode(PAGE_HELPUS));

        //Окончательная структура
        $MAP->addChild($INDEX);
        $MAP->addChild($MAGAZINE);
        $MAP->addChild($BLOG);
        $MAP->addChild($TRAININGS);
        $MAP->addChild($FEEDBACK);

        return $MAP;
    }

    public function getStructure() {
        $cacheId = AuthManager::isAuthorized() ? 'a' : 'na' . '_structure';

        $structure = PSCache::POSTS()->getFromCache($cacheId);
        if (!is_array($structure)) {
            PsProfiler::inst(__CLASS__)->start('Build structure');
            $structure = $this->getStructureImpl()->toArray();
            PsProfiler::inst(__CLASS__)->stop();
            PSCache::POSTS()->saveToCache($structure, $cacheId);
        }
        return $structure;
    }

    /**
     * Возвращает все ссылки проекта, не содержащие якоря #
     */
    public function getRealHrefs() {
        return $this->getStructureImpl()->getRealHrefs();
    }

    /** @return NavigationManager */
    public static function inst() {
        return parent::inst();
    }

    protected function __construct() {
        $this->LOGGER = PsLogger::inst(__CLASS__);
    }

}

?>
