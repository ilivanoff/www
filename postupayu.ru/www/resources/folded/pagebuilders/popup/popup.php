<?php

class PB_popup extends AbstractPageBuilder {

    public static function registerWebPages() {
        WebPages::register('popup.php', 'Всплывающее окно', PAGE_POPUP, self::getIdent(), AuthManager::AUTH_TYPE_NO_MATTER, PAGE_POPUP);
    }

    /** @var PopupPagesManager */
    private $PPM;

    /** @var BasePopupPage */
    private $popupPage;

    protected function doProcess(PageContext $ctxt, RequestArrayAdapter $requestParams, ArrayAdapter $buildParams) {
        $this->PPM = PopupPagesManager::inst();

        $this->popupPage = $this->PPM->getCurPage();
        $this->popupPage->checkAccess();

        $this->popupPage->doProcess($requestParams);
    }

    protected function doBuild(PageContext $ctxt, PageBuilderContext $builderCtxt, RequestArrayAdapter $requestParams, ArrayAdapter $buildParams) {
        //1. ЗАГОЛОВОК
        $builderCtxt->setTitle($this->popupPage->getTitle());


        //2. JAVASCRIPT
        $builderCtxt->setJsParams($this->popupPage->getJsParams());


        //3. SMARTY RESOURCES
        $builderCtxt->setSmartyParam4Resources('IDENT', $this->popupPage->getIdent());
        $builderCtxt->setSmartyParam4Resources('MATHJAX_DISABLE', true);
        $builderCtxt->setSmartyParams4Resources($this->popupPage->getSmartyParams4Resources());


        //4. GET SMARTY PARAMS FOR TPL
        $smartyParams['page'] = $this->popupPage;
        $smartyParams['content'] = $this->PPM->getPopupPageContent($this->popupPage);
        $smartyParams['header'] = PopupPagesManager::inst()->isShowPageHeader();
        $smartyParams['list'] = $this->popupPage->getIdent() == PP_404::getIdent();

        return $smartyParams;
    }

    public function getProfiler() {
        return PsProfiler::inst(__CLASS__);
    }

}

?>