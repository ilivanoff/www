<?php

class IdentPagesManager extends IdentPagesResources implements PanelFolding {

    const LIST_OFFICE = 'office';     //Элементы офиса
    const LIST_CONTROLS = 'controls'; //Список кнопок управления в верхней панели

    protected function isIncludeToList($ident, $list) {
        $office = is_subclass_of($this->ident2className($ident), 'BaseOfficePage');
        if ($list == self::LIST_CONTROLS) {
            return !$office && ($ident != IP_sitemap::getIdent());
        }
        if ($list == self::LIST_OFFICE) {
            return $office;
        }
        return true;
    }

    /** @return BaseIdentPage */
    public function getPage($ident) {
        return $ident instanceof BaseIdentPage ? $ident : $this->getEntityClassInst($ident);
    }

    public function getCurPageIdent() {
        return RequestArrayAdapter::inst()->str(IDENT_PAGE_PARAM);
    }

    /** @return BaseIdentPage */
    public function getCurPage() {
        return $this->getPage($this->getCurPageIdent());
    }

    public function getCurPageContent() {
        return $this->getPageContent($this->getCurPageIdent(), RequestArrayAdapter::inst());
    }

    /** @return IdentPageContent */
    private function getPageContent($ident, ArrayAdapter $params) {
        return $this->getPage($ident)->getContent($params);
    }

    public function getFoldedEntityPreview($ident) {
        return array(
            'info' => '',
            'content' => $this->getPageContent($ident, ArrayAdapter::inst())->getContent()
        );
    }

    /**
     * Метод возвращает ссылку, открывающую загружаемую страницу
     * 
     * <a title="Новости" class="ip-opener" href="#news"><img alt="news.png" src="/autogen/images/16x16/folding/idents/news.png"></a>
     * 
     * @param BaseIdentPage or String $item
     * @param array $params - параметры ссылки
     * @param type $content - содержимое ссылки. Если нет, то будет подставлена картинка.
     */
    public function getIdentPageHref($item, array $params = array(), $content = null) {
        return $this->getPage($item)->getIdentPageHref($params, $content);
    }

    /*
     * ПАНЕЛИ
     */

    const PANEL_CONTROLHREFS = 'CONTROLHREFS'; //Панель загружаемых страниц (справа вверху на клиенте)
    const PANEL_CLIENTOFFICE = 'CLIENTOFFICE'; //Панель личного кабинета пользователя (справа)

    public function buildPanel($panelName) {
        switch ($panelName) {
            case self::PANEL_CONTROLHREFS:
                return new IdentPagesClientPanel($this->getUserAcessibleClassInstsFromList(self::LIST_CONTROLS));
            case self::PANEL_CLIENTOFFICE:
                return new IdentPagesClientOfficePanel($this->getUserAcessibleClassInstsFromList(self::LIST_OFFICE));
        }
    }

    /**
     * Метод загружает события по переданному пользователю. Все эти события могут отражаться в маленьком личном кабинете пользователя.
     * 
     * @param PsUser $user
     */
    public function getCurrentUserEvents() {
        $events = array();
        if (AuthManager::isAuthorized()) {
            $this->profilerStart(__FUNCTION__);
            try {
                $this->LOGGER->info(' >> Запрошен список события для текущего пользователя');
                //Мы должны пробежаться по офисным страницам пользователя, которые поддерживают состояния, и запросить эти состояния
                foreach ($this->getUserAcessibleClassInstsFromList(self::LIST_OFFICE) as $office) {
                    if (!($office instanceof NumerableOfficePage)) {
                        continue;
                    }
                    $state = $office->getNumericState();
                    if ($state) {
                        $events[$office->getIdent()] = $state;
                    }
                }
                $this->LOGGER->info(' << Список событий успешно загружен: ' . array_to_string($events, false));
            } catch (Exception $ex) {
                $this->LOGGER->info('Произошла ошибка: ' . $ex->getMessage());
                $this->profilerStop(false);
                throw $ex;
            }
            $this->profilerStop();
        }
        return $events;
    }

    /** @return IdentPagesManager */
    public static function inst() {
        return parent::inst();
    }

}

?>