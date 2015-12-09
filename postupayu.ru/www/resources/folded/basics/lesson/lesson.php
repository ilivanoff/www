<?php

class BP_lesson extends BasicPage {

    public static function registerWebPages() {
        WebPages::register('lesson.php', 'Занятие', PAGE_LESSON, PB_basic::getIdent(), AuthManager::AUTH_TYPE_NO_MATTER, BASE_PAGE_TRAININGS);
    }

    public function doProcess(RequestArrayAdapter $params) {
        
    }

    public function buildContent() {
        echo $this->getFoldedEntity()->fetchTpl(array('postCP' => TrainManager::inst()->getCurrentPostContentProvider()));
    }

    public function getJsParams() {
        
    }

    public function getSmartyParams4Resources() {
        
    }

}

?>