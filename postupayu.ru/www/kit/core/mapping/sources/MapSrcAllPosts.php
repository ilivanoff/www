<?php

/**
 * Все посты системы
 *
 * @author azazello
 */
class MapSrcAllPosts extends MappingSource {

    private $posts = array();

    protected function init($mident, array $params) {
        
    }

    protected function preload($mident, array $params) {
        /* @var $pp PostsProcessor */
        foreach (Handlers::getInstance()->getPostsProcessors() as $pp) {
            $folding = $pp->getFolding();
            foreach ($folding->getAccessibleDbIdents() as $ident) {
                $this->posts[] = $folding->getUnique($ident);
            }
        }
    }

    protected function loadIdentsLeft($mident, array $params) {
        return $this->posts;
    }

    protected function loadIdentsRight($mident, array $params, MappingSource $cfgLeft, $lident) {
        switch ($mident) {
            case 'RECOMMENDED_POSTS':
                if ($cfgLeft instanceof MapSrcFoldingDb) {
                    /* @var $folding FoldedResources */
                    $folding = $cfgLeft->getFolding();
                    return array_diff($this->posts, array($folding->getUnique($lident)));
                }
                break;
        }
        return $this->posts;
    }

    protected function loadDescription($mident, array $params) {
        return 'Все посты';
    }

}

?>