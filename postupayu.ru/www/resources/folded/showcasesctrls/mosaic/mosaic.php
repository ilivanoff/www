<?php

class SC_mosaic extends ShowcasesControllerItem {

    public function init() {
        
    }

    public function getName() {
        return 'Мозаичный просмотр постов' . $this->ctxt->getSuffix();
    }

    public function getJsParams() {
        
    }

    protected function tplSmartyParams() {
        
    }

    public function getSmartyParams4Resources() {
        
    }

    public function getPlugins() {
        return array(array('calendar', 'Календарь'));
    }

}

?>