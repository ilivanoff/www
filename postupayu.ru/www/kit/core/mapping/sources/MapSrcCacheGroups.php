<?php

/**
 * Все группы кеширования
 *
 * @author azazello
 */
class MapSrcCacheGroups extends MappingSource {

    protected function init($mident, array $params) {
        
    }

    protected function preload($mident, array $params) {
        
    }

    protected function loadDescription($mident, array $params) {
        return 'Группы кеширования';
    }

    protected function loadIdentsLeft($mident, array $params) {
        return PSCache::getCacheGroups();
    }

    protected function loadIdentsRight($mident, array $params, \MappingSource $srcLeft, $lident) {
        return PSCache::getCacheGroups();
    }

}

?>
