<?php

class ClientBoxManager extends ClientBoxResources implements PanelFolding {

    const LIST_ADMIN = 'admin';   //Элементы, отображаемые справа на странице для админа
    const LIST_CLIENT = 'client'; //Элементы, отображаемые справа на странице для клиента


    /**
     * ПАНЕЛИ
     */
    const PANEL_RCOLUMN = 'RCOLUMN'; //Колонка элементов, доступных пользователю, с правой стороны сайта

    public function buildPanel($panelName) {
        $listName = AuthManager::isAuthorized() ? self::LIST_ADMIN : self::LIST_CLIENT;
        return new ClientBoxPanel($this->getUserAcessibleClassInstsFromList($listName));
    }

    /** @return ClientBoxManager */
    public static function inst() {
        return parent::inst();
    }

}

?>