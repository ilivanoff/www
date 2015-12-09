<?php

class CB_helpus extends BaseClientBox {

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    protected function getClientBoxFilling() {
        $page = WebPage::inst(PAGE_HELPUS);
        return new ClientBoxFilling($page->getName(), true, $page->getUrl());
    }

}

?>