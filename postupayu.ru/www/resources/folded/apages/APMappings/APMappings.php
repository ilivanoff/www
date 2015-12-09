<?php

class AP_APMappings extends BaseAdminPage {

    public function title() {
        return 'Маппинги';
    }

    public function buildContent() {
        return $this->getFoldedEntity()->fetchTpl(array('mappings' => AdminMappings::inst()->getAllMappings()));
    }

}

?>