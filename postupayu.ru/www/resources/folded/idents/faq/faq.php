<?php

class IP_faq extends BaseIdentPage {

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    public function getTitle() {
        return 'Часто задаваемые вопросы';
    }

    protected function processRequest(\ArrayAdapter $params) {
        return new IdentPageFilling();
    }

}

?>