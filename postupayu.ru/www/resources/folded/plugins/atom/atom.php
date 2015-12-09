<?php

class PL_atom extends BasePlugin {

    public function getName() {
        return 'Строение атома';
    }

    public function getDescr() {
        return 'Приложение позволяет наглядно увидеть связь между строением атома и его параметрами в таблице Менделеева.';
    }

    public function getPluginContent($content, ArrayAdapter $params, PluginFetchingContext $ctxt) {
        return new PluginContent($this->getFoldedEntity()->fetchTpl());
    }

}

?>
