<?php

class PB_pattern extends AbstractPageBuilder {

    public static function registerWebPages() {
        //HtmlPages::register('xxx.php', 'Консоль администратора', PAGE_ADMIN, self::getIdent(), AuthManager::AUTH_TYPE_NO_MATTER);
    }

    /**
     * Метод позволит подготовить параметры, необходимые для отображения страницы.
     * Здесь - самое место, чтобы обработать сабмит формы и выполнить редирект, чтобы не дожидаться полного построения.
     */
    protected function doProcess(PageContext $ctxt, RequestArrayAdapter $requestParams, ArrayAdapter $buildParams) {
        
    }

    protected function doBuild(PageContext $ctxt, PageBuilderContext $builderCtxt, RequestArrayAdapter $requestParams, ArrayAdapter $buildParams) {
        //1. ЗАГОЛОВОК
        $builderCtxt->setTitle('Моя страница');


        //2. JAVASCRIPT
        $jsParams['param1'] = $ctxt->getPostId();
        $jsParams['param2'] = 'My value';
        $builderCtxt->setJsParams($jsParams);


        //3. SMARTY RESOURCES
        $builderCtxt->setSmartyParam4Resources('TIMELINE_ENABE', true);


        //4. GET SMARTY PARAMS FOR TPL
        $smartyParams['host'] = ServerArrayAdapter::HTTP_HOST();

        return $smartyParams;
    }

    /**
     * Профайлер, который будет использован для профилирования страницы
     */
    public function getProfiler() {
        return null;
    }

}

?>