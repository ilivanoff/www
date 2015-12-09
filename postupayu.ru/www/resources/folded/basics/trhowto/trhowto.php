<?php

class BP_trhowto extends BasicPage {

    public static function registerWebPages() {
        WebPages::register('trhowto.php', 'Как работать с занятиями', PAGE_LESSON_HOW_TO, PB_basic::getIdent(), AuthManager::AUTH_TYPE_NO_MATTER, BASE_PAGE_TRAININGS);
    }

    public function doProcess(RequestArrayAdapter $params) {
        
    }

    public function buildContent() {
        echo $this->getFoldedEntity()->fetchTpl();
    }

    public function getJsParams() {
        
    }

    public function getSmartyParams4Resources() {
        
    }

}

?>