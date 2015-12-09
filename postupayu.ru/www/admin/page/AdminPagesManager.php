<?php

class AdminPagesManager {

    private $pages;
    private $curPage;

    private function __construct() {
        $this->pages = APagesResources::inst()->getAllUserAcessibleClassInsts();
        $this->curPage = $this->getPage(RequestArrayAdapter::inst()->str(GET_PARAM_PAGE));
        $this->curPage = $this->curPage ? $this->curPage : $this->getPage(AP_APCommon::getPageIdent());
    }

    /**
     * Сохранение разметки администраторского меню
     */
    public function saveLayout(array $layout) {
        DirItem::inst(__DIR__, __CLASS__, 'layout')->saveArrayToFile($layout);
    }

    /**
     * Загрузка разметки администраторского меню
     */
    public function getLayout() {
        $store = DirItem::inst(__DIR__, __CLASS__, 'layout')->getArrayFromFile();
        $store = to_array($store);

        //Получим массив - копию страниц и будем удалять из него те страницы, которые входят в Layout
        $pages = $this->pages;

        //Базовую страницу всегда показываем в самом верху (единственная ссылка первого уровня)
        $LAY = new AdminPagesNavigation();
        $LAY->addItem($this->getHrefToPage(AP_APCommon::getPageIdent()), 1);
        unset($pages[AP_APCommon::getPageIdent()]);

        //0 - группа, 1 - элемент
        foreach ($store as $item) {
            $type = $item[0];
            $value = $item[1];

            switch ($type) {
                //Группа
                case 0:
                    $LAY->addItem($value, 1);
                    break;
                //Элемент
                case 1:
                    if ($this->hasPage($value) && ($value != AP_APCommon::getPageIdent())) {
                        unset($pages[$value]);
                        $LAY->addItem($this->getHrefToPage($value), 2);
                    }
                    break;
            }
        }

        //Оставшие страницы добавим в общую группу
        if (count($pages) > 0) {
            $LAY->addItem('Все страницы', 1);
            foreach ($pages as $page) {
                $LAY->addItem($this->getHrefToPage($page), 2);
            }
        }

        return $LAY->getHtml();
    }

    /** @return BaseAdminPage */
    public function getPage($pageIdent) {
        if ($pageIdent instanceof BaseAdminPage) {
            return $pageIdent;
        }
        return array_get_value($pageIdent, $this->pages);
    }

    public function hasPage($page) {
        return $this->getPage($page) instanceof BaseAdminPage;
    }

    /** @return BaseAdminPage */
    public function getCurrentPage() {
        return $this->curPage;
    }

    public function pageUrl($page, array $getParams = null) {
        $getParams = to_array($getParams);
        $getParams[GET_PARAM_PAGE] = $this->getPage($page)->getPageIdent();
        return WebPage::inst(PAGE_ADMIN)->getUrl(false, $getParams);
    }

    public function getHrefToPage($page, $customTitle = null, array $getParams = null) {
        $page = $this->getPage($page);
        $url = $this->pageUrl($page, $getParams);
        $title = $customTitle ? $customTitle : $page->title();

        $htmlParams = array();
        if ($this->isCurrentPage($page)) {
            $htmlParams['class'] = to_array(array_get_value('class', $htmlParams));
            $htmlParams['class'][] = 'current';
        }
        $htmlParams['href'] = $url;
        return PsHtml::a($htmlParams, $title);
    }

    public function isCurrentPage(BaseAdminPage $page) {
        return $this->curPage->getPageIdent() == $page->getPageIdent();
    }

    public function reloadCurrentPage() {
        PsUtil::redirectTo($this->pageUrl($this->getCurrentPage()));
    }

    /*
     *
     * СИНГЛТОН
     *
     */

    private static $instance = NULL;

    /** @return AdminPagesManager */
    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new AdminPagesManager();
        }
        return self::$instance;
    }

}

?>