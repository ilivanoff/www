<?php

/**
 * Метод - фабрика классов-оболочек, которые умеют предоставлять контент различных 
 * сущностей системы: постов, рубрик и т.д.
 * 
 * Сама сущность может храниться только в базе, или иметь фолдинг на файловой системе,
 * от этого зависит, как мы можем получить доступ к её контенту. Вся эта логика инкапсулирована
 * в таких классах.
 */
final class ContentProviderFactory {

    public static function getContentProvider($item) {
        $ident = IdHelper::ident($item);
        $CACHE = SimpleDataCache::inst(__CLASS__);
        return $CACHE->has($ident) ? $CACHE->get($ident) : $CACHE->set($ident, self::makeProvider($item));
    }

    private static function makeProvider($content) {
        if ($content instanceof Rubric) {
            if ($content->isTpl()) {
                return new RubricContentProviderTpl($content);
            } else {
                return new RubricContentProviderDB($content);
            }
        }

        if ($content instanceof Post) {
            if ($content->is(POST_TYPE_ISSUE)) {
                return new IssueContentProvider($content);
            }

            if ($content->isTpl()) {
                return new PostContentProviderTpl($content);
            } else {
                return new PostContentProviderDB($content);
            }
        }

        raise_error('Не удалось построить ' . __CLASS__ . ' для ' . PsUtil::getClassName($content));
    }

}

?>