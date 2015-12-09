<?php

class BP_trainings extends BasicPage {

    public static function registerWebPages() {
        WebPages::register('trainings.php', 'Физмат кружок', BASE_PAGE_TRAININGS, PB_basic::getIdent(), AuthManager::AUTH_TYPE_NO_MATTER);
    }

    public function doProcess(RequestArrayAdapter $params) {
        
    }

    public function buildContent() {
        $tm = TrainManager::inst();
        echo $this->getFoldedEntity()->fetchTpl(array('train_rubrics' => $tm->getRubrics(), 'has_posts' => $tm->hasPosts()));
    }

    public function getJsParams() {
        
    }

    public function getSmartyParams4Resources() {
        
    }

}

?>