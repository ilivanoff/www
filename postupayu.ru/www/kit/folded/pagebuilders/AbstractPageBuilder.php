<?php

abstract class AbstractPageBuilder extends FoldedClass implements WebPagesRegistrator {

    protected function _construct() {
        //do nothing...
    }

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    /*
     * =====================
     *  ДЛЯ ПЕРЕОПРЕДЕЛЕНИЯ
     * =====================
     */

    //Предварительная проверка возможности построить страницу и выполнить действия по инициализации
    protected abstract function doProcess(PageContext $ctxt, RequestArrayAdapter $requestParams, ArrayAdapter $buildParams);

    //Построение страницы с наполнением контекста. Метод должен вернуть параметры Smatry для шаблона.
    protected abstract function doBuild(PageContext $ctxt, PageBuilderContext $builderCtxt, RequestArrayAdapter $requestParams, ArrayAdapter $buildParams);

    /*
     * Если профайлер не будет возвращён, то профилирование не проводится
     */

    /** @return BaseProfiler */
    public abstract function getProfiler();

    /*
     * ============
     *  НЕ ТРОГАТЬ
     * ============
     */

    /**
     * Предварительная обработка страницы - самое время выполнить сабмит формы, редирект и остальные подобные вещи
     */
    public final function preProcessPage(PageContext $ctxt, RequestArrayAdapter $requestParams, ArrayAdapter $buildParams) {
        $this->checkAccess();
        $this->doProcess($ctxt, $requestParams, $buildParams);
    }

    /**
     * Основная функция, выполняющая всю работу.
     * Она следит за тем, что страница была корректно построена и в ответ вурнулся PageParams.
     * 
     * @return PageParams
     */
    public final function buildPage(PageContext $ctxt, PageBuilderContext $builderCtxt, RequestArrayAdapter $requestParams, ArrayAdapter $buildParams) {
        $this->profilerStart(__FUNCTION__);
        try {
            $smartyParams = to_array($this->doBuild($ctxt, $builderCtxt, $requestParams, $buildParams));
            $pageParams = $this->foldedEntity->fetchTpl($smartyParams, FoldedResources::FETCH_RETURN_FULL_OB, true);
            check_condition($pageParams instanceof PageParams, 'После фетчинга шаблона должен быть возвращен объект PageParams');
            $this->profilerStop();
            return $pageParams;
        } catch (Exception $ex) {
            $this->profilerStop(false);
            throw $ex;
        }
    }

}

?>