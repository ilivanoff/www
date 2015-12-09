<?php

class AP_APFoldingsConstructor extends BaseAdminPage {

    public function title() {
        return 'Конструктор фолдингов';
    }

    public function buildContent() {
        return $this->foldedEntity->fetchTpl();
    }

}

?>