<?php

/**
 * Базовый класс для классов сущностей фолдинга,
 * если они, конечно, поддерживают тип PHP.
 *
 * @author azazello
 */
abstract class FoldedClass {

    /** Идентификатор сущности фолдинга */
    protected $ident;

    /** @var FoldedResources */
    protected $folding;

    /** @var FoldedEntity */
    protected $foldedEntity;

    public final function __construct(FoldedEntity $foldedEntity) {
        $this->ident = $foldedEntity->getIdent();
        $this->folding = $foldedEntity->getFolding();
        $this->foldedEntity = $foldedEntity;
        check_condition($this->ident === self::getIdent(), "Несовпадение идентификаторов в экземпляре класса для сущности $foldedEntity");

        //Позволим предкам проинициализироваться
        $this->_construct();
    }

    /*
     * МЕТОДЫ ДЛЯ ПЕРЕОПРЕДЕЛЕНИЯ
     */

    /**
     * Тип доступа, необходимый для работы с классом
     */
    public abstract function getAuthType();

    /**
     * Конструктор
     */
    protected abstract function _construct();

    /*
     * УТИЛИТНЫЕ МЕТОДЫ ЭКЗЕМПЛЯРА
     */

    /** @return FoldedEntity */
    public final function getFoldedEntity() {
        return $this->foldedEntity;
    }

    /** Метод проверяет, имеет ли авторизованный пользователь поступ к этому классу */
    public final function isUserHasAccess() {
        return AuthManager::hasAccess($this->getAuthType());
    }

    /** Метод проверяет, имеет ли авторизованный пользователь поступ к этому классу */
    protected final function checkAccess() {
        AuthManager::checkAccess($this->getAuthType());
    }

    /** Запуск профайлера */
    protected final function profilerStart($__FUNCTION__) {
        PsProfiler::inst('FoldingClass')->start(get_called_class() . '->' . $__FUNCTION__);
    }

    /** Остановка профайлера */
    protected final function profilerStop($save = true) {
        PsProfiler::inst('FoldingClass')->stop($save);
    }

    /** @return PsLoggerInterface - логгер для данного класса сущности фолдинга */
    protected final function LOGGER() {
        return PsLogger::inst(get_called_class());
    }

    /*
     * СТАТИЧНЫЕ УТИЛИТНЫЕ МЕТОДЫ
     */

    /** @return FoldedClass */
    protected static function inst() {
        return Handlers::getInstance()->getFoldingByClassPrefix(FoldedResources::extractPrefixFromClass(get_called_class()))->
                        getEntityClassInst(self::getIdent());
    }

    //Идентификатор сущности фолдинга внутри фолдинга
    public final static function getIdent() {
        return FoldedResources::extractIdentFormClass(get_called_class());
    }

    public function __toString() {
        return get_called_class();
    }

}

?>