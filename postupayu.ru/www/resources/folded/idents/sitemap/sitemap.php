<?php

class IP_sitemap extends BaseIdentPage {

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    public function getTitle() {
        return 'Карта сайта';
    }

    protected function processRequest(\ArrayAdapter $params) {
        raise_error('Карта сайта строится только на клиенте');
    }

}

?>