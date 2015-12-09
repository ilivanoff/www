<?php

class PopupPagesManager extends PopupPagesResources {
    /**
     * Типы popup плагинов (плагинов, которые могут быть отображены в popup окне или popup старниц, работающих как плагины)
     */

    const TYPE_PAGE = 'P';
    const TYPE_PLUGIN = 'L';

    /**
     * Константы для кеша
     */
    const CACHABLE_VISIBLE = 'VISIBLE';
    const CACHABLE_DEFAULT = 'DEFAULT';

    /**
     * Плагины, использованные в постах
     */
    public function getSnapshot() {
        $SNAPSOT = PSCache::POPUPS()->getFromCache('POPUP_PAGES_SNAPSOT', PsUtil::getClassConsts(__CLASS__, 'CACHABLE_'));
        if (!is_array($SNAPSOT)) {

            $this->LOGGER->info('Building plugins SNAPSHOT...');

            //Сначала соберём все плагины, использованные в постах
            $USED = array();
            /* @var $pp PostsProcessor */
            foreach (Handlers::getInstance()->getPostsProcessors() as $pp) {
                $pp->preloadAllPostsContent();
                /* @var $post Post */
                foreach ($pp->getPosts() as $post) {
                    $plugins = $pp->getPostContentProvider($post->getId())->getPostParams()->getUsedPlugins();
                    if (empty($plugins)) {
                        //В посте не используются плагины
                        continue;
                    }
                    $this->LOGGER->info('Plugins [{}] used in post [{}].', implode(',', $plugins), IdHelper::ident($post));
                    $USED = array_merge($USED, $plugins);
                }
            }
            $USED = array_unique($USED);

            $this->LOGGER->info('Full list of used plugins: [{}].', implode(',', $USED));

            //Соберём все видимые попап-страницы и плагины
            $ENTITYS = array();
            foreach (array($this->getVisibleClassInsts(), PluginsManager::inst()->getVisibleClassInsts()) as $popups) {
                foreach ($popups as $ident => $popup) {
                    $visType = $popup->getPopupVisibility();
                    $take = PopupVis::isAllwaysVisible($visType) || ($visType == PopupVis::BYPOST && in_array($ident, $USED));
                    if ($take) {
                        $ENTITYS[] = $popup;
                    }
                }
            }

            //Отсортируем собранные сущности по названию
            usort($ENTITYS, function($e1, $e2) {
                        $str1 = $e1 instanceof BasePopupPage ? $e1->getTitle() : $e1->getName();
                        $str2 = $e2 instanceof BasePopupPage ? $e2->getTitle() : $e2->getName();
                        return strcasecmp($str1, $str2);
                    });

            $VISIBLE = array();
            $DEFAULT = array();
            foreach ($ENTITYS as $entity) {
                $visType = $entity->getPopupVisibility();
                if ($entity instanceof BasePopupPage) {
                    $type = self::TYPE_PAGE;
                    $ident = $entity->getIdent();
                }
                if ($entity instanceof BasePlugin) {
                    $type = self::TYPE_PLUGIN;
                    $ident = $entity->getIdent();
                }

                $VISIBLE[$type . '_' . $ident] = array('type' => $type, 'ident' => $ident);
                if ($visType == PopupVis::TRUE_DEFAULT) {
                    $DEFAULT[$type . '_' . $ident] = array('type' => $type, 'ident' => $ident);
                }
            }

            $SNAPSOT = array(
                self::CACHABLE_VISIBLE => $VISIBLE,
                self::CACHABLE_DEFAULT => $DEFAULT
            );

            $SNAPSOT = PSCache::POPUPS()->saveToCache($SNAPSOT, 'POPUP_PAGES_SNAPSOT');
        }
        return $SNAPSOT;
    }

    /** @return BasePopupPage */
    public function getPage($ident) {
        return $this->getEntityClassInst($ident);
    }

    //ВИДИМЫЕ страницы, разбитые по типам
    private function getVisiblePages() {
        return array_get_value(self::CACHABLE_VISIBLE, $this->getSnapshot(), array());
    }

    public function isPageVisible($type, $ident) {
        return array_key_exists($type . '_' . $ident, $this->getVisiblePages());
    }

    protected function isPageAsPlugin($ident) {
        return $this->isPageVisible(self::TYPE_PAGE, $ident);
    }

    /**
     * Возвращает тип popup-visibility для плагина.
     */
    private function getPagePopupVisibility($type, $ident) {
        switch ($type) {
            case self::TYPE_PAGE:
                return $this->getPage($ident)->getPopupVisibility();
            case self::TYPE_PLUGIN:
                return PluginsManager::inst()->getPlugin($ident)->getPopupVisibility();
        }
        check_condition(false, "Неизвестный тип страницы: [$type].");
    }

    //ДЕФОЛТНЫЕ страницы
    private function getDefaultPages() {
        return array_get_value(self::CACHABLE_DEFAULT, $this->getSnapshot(), array());
    }

    /**
     * Валидация запроса
     */
    public function isValidPageRequested() {
        return is_array($this->getRequestParams());
    }

    public function getRequestParams() {
        $PARAMS = array();
        $RQ = RequestArrayAdapter::inst();

        //СТРАНИЦА
        if (!$RQ->has(POPUP_WINDOW_PARAM)) {
            return $PARAMS; //---
        }

        $pageIdent = $RQ->str(POPUP_WINDOW_PARAM);
        if (!$this->hasAccess($pageIdent, true)) {
            return false;
        }
        $PARAMS[POPUP_WINDOW_PARAM] = $pageIdent;

        if ($pageIdent != PP_plugin::getIdent()) {
            return $PARAMS;
        }

        //ПЛАГИН
        if (!$RQ->has(GET_PARAM_PLUGIN_IDENT)) {
            return false; //---
        }

        $pluginIdent = $RQ->str(GET_PARAM_PLUGIN_IDENT);
        if (!$this->isPageVisible(self::TYPE_PLUGIN, $pluginIdent)) {
            return false;
        }
        $PARAMS[GET_PARAM_PLUGIN_IDENT] = $pluginIdent;

        return $PARAMS;
    }

    //Возвращает страницу, которая будет построена
    /** @return BasePopupPage */
    public function getCurPage() {
        $GA = RequestArrayAdapter::inst();
        return $GA->has(POPUP_WINDOW_PARAM) ? $this->getPage($GA->str(POPUP_WINDOW_PARAM)) : $this->getPage(PP_404::getIdent());
    }

    //Если показываемая страница отображается как плагин, то для неё будет показан заголовок
    public function isShowPageHeader() {
        $ident = $this->getCurPage()->getIdent();

        $headerPages[] = PP_404::getIdent();
        $headerPages[] = PP_plugin::getIdent();

        return in_array($ident, $headerPages) || $this->isPageAsPlugin($ident);
    }

    //ИЗБРАННЫЕ страницы пользователя

    private $USER_FAVORITES;

    private function getCurrentUserFavorites() {
        if (isset($this->USER_FAVORITES)) {
            return $this->USER_FAVORITES;
        }
        $this->USER_FAVORITES = array();

        if (!AuthManager::isAuthorized()) {
            return $this->USER_FAVORITES;
        }

        $dataArr = PopupBean::inst()->getUserFavorites(AuthManager::getUserId());
        foreach ($dataArr as $data) {
            $type = $data['v_type'];
            $ident = $data['v_ident'];
            //Обязательна проверка на видимость, так как плагин может быть отключён
            if ($this->isPageVisible($type, $ident)) {
                $this->USER_FAVORITES[$type . '_' . $ident] = array('type' => $type, 'ident' => $ident);
            }
        }
        return $this->USER_FAVORITES;
    }

    private function isCurrentUserFavorite($type, $ident) {
        return array_key_exists($type . '_' . $ident, $this->getCurrentUserFavorites());
    }

    /**
     * Ссылки на popup-страницы
     */
    public function getPageUrl($page, array $params = array()) {
        $ident = $page instanceof BasePopupPage ? $page->getIdent() : $page;
        $this->assertExistsEntity($ident);
        $params[POPUP_WINDOW_PARAM] = $ident;
        return WebPage::inst(PAGE_POPUP)->getUrl(false, $params);
    }

    /**
     * Список страниц в виде массива:
     * {
     * type,
     * ident,
     * fav,
     * cover,
     * name,
     * url
     * }
     */
    private function getPagesInfo($typeIdentArr) {
        $RESULT = array();

        $PLM = PluginsManager::inst();

        foreach ($typeIdentArr as $id => $page) {
            $type = $page['type'];
            $ident = $page['ident'];

            $item['id'] = $id; //id - уникальная связка типа и идентификатора
            $item['type'] = $type;
            $item['ident'] = $ident;
            $item['fav'] = $this->isCurrentUserFavorite($type, $ident) ? 1 : 0;

            switch ($type) {
                case self::TYPE_PAGE:
                    $popup = $this->getPage($ident);
                    $item['name'] = $popup->getTitle();
                    $item['url'] = $this->getPageUrl($popup);
                    $item['cover'] = $this->getCover($ident, '36x36')->getRelPath();
                    $item['descr'] = $popup->getDescr();
                    break;

                case self::TYPE_PLUGIN:
                    $plugin = $PLM->getPlugin($ident);
                    $item['name'] = $plugin->getName();
                    $item['url'] = $this->getPageUrl(PP_plugin::getIdent(), array(GET_PARAM_PLUGIN_IDENT => $ident));
                    $item['cover'] = $PLM->getCover($ident, '36x36')->getRelPath();
                    $item['descr'] = $plugin->getDescr();
                    break;
            }

            $RESULT[] = $item;
        }

        return $RESULT;
    }

    /**
     * Данные для построения списков плагинов
     */
    public function getPagesList() {
        return $this->getPagesInfo($this->getVisiblePages());
    }

    public function getCurrentUserPagesList() {
        return $this->getPagesInfo(AuthManager::isAuthorized() ? $this->getCurrentUserFavorites() : $this->getDefaultPages());
    }

    /**
     * Привязывает дефолтные страницы к пользователю посте регистрации
     */
    public function bindDefaultPages2User($userId) {
        PopupBean::inst()->saveUserPlugins($userId, $this->getDefaultPages());
    }

    /**
     * Обновляет порядок планинов пользователя после сортировки.
     */
    public function updateCurrentUserPagesOrder(array $items) {
        $cnt = count($items);
        if ($cnt == 0) {
            return 'Состояния не переданы';
        }

        $favorites = $this->getCurrentUserFavorites();

        if (count($favorites) != $cnt) {
            return 'Переданное и текущее состояния не совпадают';
        }

        foreach ($items as $item) {
            $id = $item['type'] . '_' . $item['ident'];
            if (!array_key_exists($id, $favorites)) {
                return 'Переданное и текущее состояния не совпадают';
            }
        }

        PopupBean::inst()->saveUserPlugins(AuthManager::getUserId(), $items);

        return true;
    }

    /**
     * Урл для pageIdent будем спрашивать менеджера всплывающих окон, так как он может знать о том,
     * что плагинам вообще запрещено открываться в отдельных окнах.
     */
    public function getPluginUrl(BasePlugin $plugin) {
        if (!PopupVis::isCanBeVisible($plugin->getPopupVisibility())) {
            return null;
        }
        //Во всех других случаях добавим ссылку на открытие плагина
        return PsUrl::addParams(PP_plugin::getIdent(), array(GET_PARAM_PLUGIN_IDENT => $plugin->getIdent()));
    }

    /**
     * Метод фактически строит страницу.
     * Нам нужно выполнить множество различных действий, поэтому перенесём все их сюда.
     * К моменту выполнения у страницы уже вызван метод doProcess
     */
    public function getPopupPageContent(BasePopupPage $page) {
        return $this->getResourcesLinks($page->getIdent(), ContentHelper::getContent($page));
    }

    /**
     * Предпросмотр страницы при редактировании
     */
    public function getFoldedEntityPreview($ident) {
        $page = $this->getPage($ident);
        $page->doProcess(ArrayAdapter::inst());
        return array(
            'info' => $page->getTitle(),
            'content' => $this->getPopupPageContent($page)
        );
    }

    /**
     * Привязка popup-страницы к пользователю.
     * Метод должен работать максимально быстро - не будем проверять видимость страницы, просто проверим,
     * существует ли она.
     */
    public function toggleUserPopup($isAdd, $type, $ident) {
        $canBeVis = PopupVis::isCanBeVisible($this->getPagePopupVisibility($type, $ident));
        check_condition($canBeVis, "Незарегистрированный плагин с типом [$type] и идентификатором [$ident]");
        PopupBean::inst()->toggleUserPopup(AuthManager::getUserId(), $isAdd, $type, $ident);
    }

    /** @return PopupPagesManager */
    public static function inst() {
        return parent::inst();
    }

}

?>