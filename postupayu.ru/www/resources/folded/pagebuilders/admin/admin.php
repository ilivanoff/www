<?php

class PB_admin extends AbstractPageBuilder {

    public static function registerWebPages() {
        WebPages::register('xxx.php', 'Консоль администратора', PAGE_ADMIN, self::getIdent(), AuthManager::AUTH_TYPE_NO_MATTER, PAGE_ADMIN);
    }

    /** @var BaseAdminPage */
    private $adminPage;

    /* Признак авторизованности */
    private $authed;

    protected function doProcess(PageContext $ctxt, RequestArrayAdapter $requestParams, ArrayAdapter $buildParams) {
        $this->authed = AuthManager::isAuthorizedAsAdmin();
        if ($this->authed) {
            $this->adminPage = AdminPagesManager::getInstance()->getCurrentPage();
        } else {
            if (FORM_AdminLoginForm::getInstance()->isValid4Process()) {
                if (AdminAuthManager::getInstance()->login()) {
                    WebPages::reloadCurPage();
                }
            }
        }
    }

    protected function doBuild(\PageContext $ctxt, \PageBuilderContext $builderCtxt, \RequestArrayAdapter $requestParams, \ArrayAdapter $buildParams) {
        //1. ЗАГОЛОВОК
        $builderCtxt->setTitle($this->authed ? 'Админка | ' . $this->adminPage->title() : 'xxx');


        //2. JAVASCRIPT
        $builderCtxt->setJsParams($this->authed ? $this->adminPage->getJsParams() : null);


        //3. SMARTY RESOURCES
        if ($this->authed) {
            $builderCtxt->setSmartyParams4Resources($this->adminPage->getSmartyParams4Resources());
            $builderCtxt->setSmartyParam4Resources('IDENT', $this->adminPage->getPageIdent());
            $builderCtxt->setSmartyParam4Resources('TIMELINE_ENABE', true);
        }

        $smartyParams['authed'] = $this->authed;
        if (!$this->authed) {
            return $smartyParams;
        }

        //Запустим неограниченный по времени режим - мало ли, что мы там будем делать:)
        PsUtil::startUnlimitedMode();

        //Получаем содержимое админской страницы
        $content = ContentHelper::getContent($this->adminPage);
        //Добавляем к ней ресурсы
        $content = APagesResources::inst()->getResourcesLinks($this->adminPage->getPageIdent(), $content);

        $smartyParams['page'] = $this->adminPage;
        $smartyParams['content'] = $content;
        $smartyParams['pagesLayout'] = AdminPagesManager::getInstance()->getLayout();

        return $smartyParams;
    }

    public function getProfiler() {
        return null;
    }

}

?>