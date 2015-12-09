<?php

/**
 * Базовый класс для всех хранилищь, предоставляющих информацию о менеджерах фолдингов.
 * 
 * @author azazello
 */
abstract class FoldingsProviderAbstract {

    /**
     * Массив фолдингов
     */
    public abstract static function listFoldings();

    /**
     * Название класса
     */
    public static final function calledClass() {
        return get_called_class();
    }

    /**
     * Название класса
     */
    public static final function isSdk() {
        return self::calledClass() === FoldingsProviderSdk::calledClass();
    }

    /**
     * Название класса
     */
    public static final function isInScope($scope) {
        switch ($scope) {
            case ENTITY_SCOPE_ALL:
                return true;
            case ENTITY_SCOPE_SDK:
                return self::isSdk();
            case ENTITY_SCOPE_PROJ:
                return !self::isSdk();
        }
        return raise_error("Invalid entity scope [$scope]");
    }

}

?>