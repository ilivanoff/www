<?php

class BP_filing extends BasicPage {

    public static function registerWebPages() {
        WebPages::register('filing.php', 'Все занятия в разделе', PAGE_FILING, PB_basic::getIdent(), AuthManager::AUTH_TYPE_NO_MATTER, BASE_PAGE_TRAININGS);
    }

    public function doProcess(RequestArrayAdapter $params) {
        
    }

    public function buildContent() {
        echo $this->getFoldedEntity()->fetchTpl(array('rubricCP' => TrainManager::inst()->getCurrentRubricContentProvider()));
    }

    public function getJsParams() {
        
    }

    public function getSmartyParams4Resources() {
        
    }

}

?>