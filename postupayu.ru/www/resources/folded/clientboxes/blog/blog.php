<?php

class CB_blog extends BaseClientBox {

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    protected function getClientBoxFilling() {
        return new ClientBoxFilling('Рубрики блога:', false, BASE_PAGE_BLOG);
    }

}

?>