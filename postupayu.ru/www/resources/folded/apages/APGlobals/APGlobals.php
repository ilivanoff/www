<?php

class AP_APGlobals extends BaseAdminPage {

    public function title() {
        return 'Глобальные настройки';
    }

    public function buildContent() {
        $PARAMS['props'] = PsGlobals::inst()->getProps();
        echo $this->getFoldedEntity()->fetchTpl($PARAMS);
    }

    public function getSmartyParams4Resources() {
        return array('MATHJAX_DISABLE' => true);
    }

}

?>