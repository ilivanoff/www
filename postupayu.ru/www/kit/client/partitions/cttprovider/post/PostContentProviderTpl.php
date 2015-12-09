<?php

class PostContentProviderTpl extends PostContentProvider {

    const PARAMS_CACHE_KEY = 'FetchParams';

    /** @return FetchParams */
    public function getPostContent($cached = true) {
        return $this->fetchPost(self::FETCH_TYPE_CONTENT, $cached);
    }

    /** @return FetchParams */
    public function getPostContentShowcase($cached = true) {
        return $this->fetchPost(self::FETCH_TYPE_CONTENT_SHOWCASE, $cached);
    }

    /** @return FetchParams */
    public function getPostParams($cached = true) {
        return $this->fetchPost(self::FETCH_TYPE_PARAMS, $cached);
    }

    const FETCH_TYPE_PARAMS = 1;
    const FETCH_TYPE_CONTENT = 2;
    const FETCH_TYPE_CONTENT_SHOWCASE = 3;

    private final function fetchPost($fetchType, $cached) {
        $entity = $this->pp()->getFolding()->getFoldedEntity($this->postContent->getIdent());

        switch ($fetchType) {
            case self::FETCH_TYPE_PARAMS:
                $smartyParams['showcase_mode'] = false;
                $cacheId = $cached ? simple_hash($smartyParams) : null;
                return $entity->fetchTpl($smartyParams, FoldedResources::FETCH_RETURN_PARAMS_OB, false, $cacheId);

            case self::FETCH_TYPE_CONTENT:
                $smartyParams['showcase_mode'] = false;
                $cacheId = $cached ? simple_hash($smartyParams) : null;
                return $entity->fetchTpl($smartyParams, FoldedResources::FETCH_RETURN_FULL_OB, true, $cacheId);

            case self::FETCH_TYPE_CONTENT_SHOWCASE:
                $smartyParams['showcase_mode'] = true;
                $cacheId = $cached ? simple_hash($smartyParams) : null;
                return $entity->fetchTpl($smartyParams, FoldedResources::FETCH_RETURN_FULL_OB, $cacheId);
        }

        raise_error("Неизвестный тип фетчинга поста: [$fetchType]");
    }

}

?>