<?php

class AP_APMissprint extends BaseAdminPage {

    public function title() {
        return 'Опечатки';
    }

    public function buildContent() {
        $PARAMS['mp'] = UtilsBean::inst()->getMissprints();
        echo $this->getFoldedEntity()->fetchTpl($PARAMS);
    }

}

?>