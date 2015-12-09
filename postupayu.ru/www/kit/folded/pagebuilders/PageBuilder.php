<?php

final class PageBuilder extends PageBuilderResources {

    public function registerAllHtmlPages() {
        /** @var WebPagesRegistrator */
        foreach ($this->getAccessibleClassNames() as $builderClass) {
            $builderClass::registerWebPages();
        }
    }

    /** @return AbstractPageBuilder */
    private function getPageBuilder($pageType) {
        return $this->getEntityClassInst($pageType);
    }

    /**
     * Параметры, которые будут заменены "как есть" в конечном шаблоне.
     * Необходимы для того, чтобы избежать замены передаваемых значений фильтрами Smarty.
     */
    private $ASIS_VALUES = array();

    /**
     * Метод добавит значение и вернёт макрос, пригодный для вставки в html.
     */
    private function addAsIsValue($value) {
        $num = 1 + count($this->ASIS_VALUES);
        $macros = "[[ASIS:$num]]";
        $this->ASIS_VALUES[$macros] = $value;

        $this->LOGGER->infoBox("Registered $macros");
        $this->LOGGER->info($value);

        return $macros;
    }

    /**
     * Производим замену значений, отображаемых "как есть".
     */
    private function replaceAsIsValues($html) {
        foreach ($this->ASIS_VALUES as $macros => $value) {
            $html = str_replace_first($macros, $value, $html);
        }
        return $html;
    }

    /*
     * ======================================
     *             JAVASCRIPT DEFS
     * ======================================
     * 
     * В JavaScript можно обращаться через defs.param_name
     */

    private function jsConsts() {
        return array(
            'FORM_PARAM_FILE' => FORM_PARAM_FILE,
            'FORM_PARAM_BUTTON' => FORM_PARAM_BUTTON,
            'SHORT_TEXT_MAXLEN' => SHORT_TEXT_MAXLEN,
            'EMAIL_MAXLEN' => EMAIL_MAXLEN,
            'REMIND_CODE_LENGTH' => REMIND_CODE_LENGTH,
            'AJAX_ACTION_PARAM' => AJAX_ACTION_PARAM,
            'PAGING_PARAM' => GET_PARAM_PAGE_NUM,
            'POPUP_WINDOW_PARAM' => POPUP_WINDOW_PARAM,
            'IDENT_PAGE_PARAM' => IDENT_PAGE_PARAM,
            'TIMELINE_LOADING_MARK' => TIMELINE_LOADING_MARK,
            'ACTIVITY_INTERVAL' => PsSettings::ACTIVITY_INTERVAL(),
            'STOCK_IDENT_PARAM' => STOCK_IDENT_PARAM,
            'STOCK_ACTION_PARAM' => STOCK_ACTION_PARAM,
            'FORM_PARAM_ID' => FORM_PARAM_ID
        );
    }

    private function jsCommon(PageContext $ctxt) {
        $params['url'] = $ctxt->getRequestUrl();
        $params['isPopup'] = $ctxt->isPopupPage();
        $params['userId'] = AuthManager::getUserIdOrNull();
        $params['isAuthorized'] = AuthManager::isAuthorized();
        $params['isDOA'] = PsSettings::DEVMODE_OR_ADMIN();
        $params['isLogging'] = PsSettings::DEVMODE_OR_ADMIN();
        $params['currentSubmitTimeout'] = ActivityWatcher::getWaitTime();
        $params['tzOffset'] = PsTimeZone::inst()->getCurrentDateTimeZone()->getOffset(new DateTime());
        $params['marker'] = AuthManager::getUserSessoinMarker();

        /* @var $folding FoldedResources */
        foreach (Handlers::getInstance()->getFoldingsIndexed() as $unique => $folding) {
            $params['foldings'][$unique] = $folding->getResourcesDm()->relDirPath();
        }

        return $params;
    }

    private function buildJsDefs(PageParams $params, PageContext $ctxt) {
        $JS_CLASS_CONSTS = PsUtil::getClassConsts('PsConstJs');
        $JS_CONSTS = $this->jsConsts();
        $JS_COMMON = $this->jsCommon($ctxt);
        $JS_PAGE = $params->getJsParams();

        $defs = json_encode(array_merge($JS_CONSTS, $JS_COMMON, $JS_PAGE));
        $const = json_encode($JS_CLASS_CONSTS);
        $defs = "var defs=$defs; var CONST=$const;";
        $defs = PsHtml::linkJs(null, $defs);

        $this->LOGGER->infoBox('JS DEFS', $defs);

        return $defs;
    }

    /*
     * ======================================
     *             PAGE RESOURCES
     * ======================================
     */

    private function buildResources(PageParams $params, PageContext $ctxt) {
        $SMARTY_PARAMS['CTXT'] = $ctxt;
        $SMARTY_PARAMS['PATH_BASE'] = $ctxt->getPage()->getPathBase();
        $SMARTY_PARAMS['COMMON_CSS_MEDIA'] = 'print';
        $SMARTY_PARAMS['JS_DEFS'] = $this->addAsIsValue($this->buildJsDefs($params, $ctxt));

        //Если в данный момент открыта попап страница с видом поста в варианте "для печати", то
        //common.print.css подключается как обычный ресурс, чтобы мы могли видеть страницу такой, 
        //какой она будет при печати.

        $SMARTY_PARAMS_PAGE = $params->getSmartyParams4Resources();

        $SMARTY_PARAMS = array_merge($SMARTY_PARAMS, $SMARTY_PARAMS_PAGE);

        $resources = PSSmarty::template('page/page_resources.tpl', $SMARTY_PARAMS)->fetch();
        $resources = trim($resources);

        $this->LOGGER->infoBox('PAGE_RESOURCES', $resources);

        return $resources;
    }

    /*
     * ======================================
     *             PAGE BUILDING
     * ======================================
     */

    //На данном этапе запрос уже провалидирован в самой WebPage
    public final function buildPage(array $buildParams = array()) {
        header('Content-Type: text/html; charset=utf-8');
        ExceptionHandler::registerPretty();

        //Запросим адаптер, чтобы сбросить параметры в сессии
        UnloadArrayAdapter::inst();

        // Подготовим необходимые классы
        $CTXT = PageContext::inst();
        $PAGE = $CTXT->getPage();
        $BUILDER = $this->getPageBuilder($CTXT->getPageType());
        $PROFILER = $BUILDER->getProfiler();

        $RESOURCES = null;
        $TITLE = null;
        $CONTENT = null;

        if ($PROFILER) {
            // Начинаем профилирование
            $PROFILER->start($CTXT->getRequestUrl());
        }

        try {
            //Подготовим объекты, которые будем передавать построителю страницы
            $RQ_PARAMS = RequestArrayAdapter::inst();
            $BUILD_PARAMS = ArrayAdapter::inst($buildParams);
            $BUILDER_CTXT = PageBuilderContext::getInstance();

            //Стартуем контекст
            $BUILDER_CTXT->setContextWithFoldedEntity($BUILDER->getFoldedEntity());

            //Вызываем предварительную обработку страницы
            $BUILDER->preProcessPage($CTXT, $RQ_PARAMS, $BUILD_PARAMS);

            // Оповещаем слушателей
            /* @var $listener PagePreloadListener */
            foreach (Handlers::getInstance()->getPagePreloadListeners() as $listener) {
                $listener->onPagePreload($PAGE);
            }

            //Билдер строит страницу, наполняя контекст. Нам от него нужны будут только данные из контекста
            $PARAMS = $BUILDER->buildPage($CTXT, $BUILDER_CTXT, $RQ_PARAMS, $BUILD_PARAMS);

            //Остановим контекст
            $BUILDER_CTXT->dropContext();

            //Загрузим параметры
            $TITLE = $PARAMS->getTitle();
            $CONTENT = $PARAMS->getContent();

            // Подключаем все необходимые ресурсы
            $RESOURCES = $this->buildResources($PARAMS, $CTXT);

            //Проведём финализацию страницы, чтобы различные менеджеры могли добавить к ней свои данные
            $CONTENT = PageFinaliserFoldings::finalize($this->LOGGER, $CONTENT);
        } catch (Exception $ex) {
            $TITLE = trim($TITLE) . ' (произошла ошибка)';
            $CONTENT = ExceptionHandler::getHtml($ex);
        }

        //Непосредственное построение страницы.
        $PAGE_PARAMS['RESOURCES'] = $RESOURCES;
        $PAGE_PARAMS['TITLE'] = $TITLE;
        $PAGE_PARAMS['CONTENT'] = $CONTENT;

        //ПОДСТАВЛЯЕМ ВСЕ ПАРАМЕТРЫ СТРАНИЦЫ В БАЗОВЫЙ ШАБЛОН
        $PAGE_CONTENT = PSSmarty::template('page/page_pattern.tpl', $PAGE_PARAMS)->fetch();
        $this->LOGGER->infoBox('HTML PAGE', $PAGE_CONTENT);

        //ФИНАЛИЗАЦИЯ СТРАНИЦЫ - ВЫЧИСЛИМ РЕСУРСЫ, КОТОРЫЕ НУЖНО ОТКЛЮЧИТЬ
        $PAGE_CONTENT = PageFinaliserRegExp::finalize($this->LOGGER, $PAGE_CONTENT);

        //НОРМАЛИЗАЦИЯ СТРАНИЦЫ - УДАЛИМ ДВОЙНЫЕ ПРОБЕЛЫ И ПЕРЕНОСЫ
        if (PsDefines::isNormalizePage()) {
            $PAGE_CONTENT = PageNormalizer::finalize($this->LOGGER, $PAGE_CONTENT);
        }

        //ВСТАВИМ ASIS ПАРАМЕТРЫ, ТАК КАК БОЛЕЕ SMARTY ФИЛЬТРЫ ВЫЗЫВАТЬСЯ НЕ БУДУТ
        $PAGE_CONTENT = $this->replaceAsIsValues($PAGE_CONTENT);
        $this->LOGGER->infoBox('PAGE WITH ASIS REPLACED', $PAGE_CONTENT);

        //BOOOM :)
        echo $PAGE_CONTENT;

        if ($PROFILER) {
            // Заканчиваем профилирование
            $PROFILER->stop();
            PageOpenWatcher::updateUserPageWatch($CTXT->getRequestUrl());
        }
    }

    /** @return PageBuilder */
    public static function inst() {
        return parent::inst();
    }

}

?>
