<?php

class DG_plugins extends BaseDialog {

    protected function getWindowTplSmartyParams() {
        $params['pages'] = PopupPagesManager::inst()->getPagesList();
        return $params;
    }

    protected function cacheGroup() {
        return PSCache::POPUPS();
    }

}

?>