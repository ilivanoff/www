<?php

/**
 * Класс хранит в себе все маппинги системы, возвращая объекты типа {@link MappingClient},
 * дающие доступ только к клиентским методам.
 *
 * @author azazello
 */
class Mappings {

    /**
     * Маппинг дополнительных плагинов предпросмотра постов на рубрики
     * 
     * @param str $postType - тип поста
     * @return MappingClient
     */
    public static final function RUBRIC_2_SCCONTROLLERS($postType) {
        $rp = Handlers::getInstance()->getRubricsProcessorByPostType($postType);
        $lunique = $rp->getRubricsFolding()->getUnique();
        $runique = ShowcasesCtrlManager::inst()->getUnique();
        return Mapping::inst(//
                        MapSrcFoldingDb::inst(array('unique' => $lunique), __FUNCTION__), //
                        MapSrcFoldingEnts::inst(array('unique' => $runique), __FUNCTION__), //
                        'Привязка вариантов предпросмотра постов к ' . ps_strtolower($rp->rubricTitle(null, 3))
        );
    }

    /**
     * Маппинг дополнительных плагинов предпросмотра постов на рубрики
     * 
     * @param str $postType - тип поста
     * @return MappingClient
     */
    public static final function RECOMMENDED_POSTS($postType) {
        $pp = Handlers::getInstance()->getPostsProcessorByPostType($postType);
        $lunique = $pp->getFolding()->getUnique();
        return Mapping::inst(//
                        MapSrcFoldingDb::inst(array('unique' => $lunique), __FUNCTION__), //
                        MapSrcAllPosts::inst(array(), __FUNCTION__), //
                        'Рекомендованные посты для ' . ps_strtolower($pp->postTitle(null, 2))
        );
    }

    /**
     * Маппинг групп кешей к фолдингам
     * 
     * @param str $postType - тип поста
     * @return MappingClient
     */
    public static final function CACHE_FOLDINGS() {
        return Mapping::inst(//
                        MapSrcCacheGroups::inst(array(), __FUNCTION__), //
                        MapSrcAllFoldings::inst(array(), __FUNCTION__), //
                        'Группы кеширования к Фолдингам'
        );
    }

    /**
     * Маппинг групп кешей к сущностям базы - таблицам и представлениям
     * 
     * @param str $postType - тип поста
     * @return MappingClient
     */
    public static final function CACHE_DBENTITYS() {
        return Mapping::inst(//
                        MapSrcCacheGroups::inst(array(), __FUNCTION__), //
                        MapSrcDbEntitys::inst(array(), __FUNCTION__), //
                        'Группы кеширования к Таблицам и представлениям'
        );
    }

    /**
     * Меод должен вернуть все возможные маппинги.
     * Нужно для показа в админке
     */
    protected final function allMappings() {
        $mappings = array();
        foreach (Handlers::getInstance()->getRubricsProcessors() as $postType => $rp) {
            $mappings[] = self::RUBRIC_2_SCCONTROLLERS($postType);
        }
        foreach (Handlers::getInstance()->getPostsProcessors() as $postType => $pp) {
            $mappings[] = self::RECOMMENDED_POSTS($postType);
        }
        $mappings[] = self::CACHE_FOLDINGS();
        $mappings[] = self::CACHE_DBENTITYS();
        return $mappings;
    }

}

?>