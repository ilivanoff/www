<?php

class eclassname extends BasePlugin {

    public function getName() {
        return 'Название плагина';
    }

    public function getDescr() {
        return 'Приложение позволяет ...';
    }

    public function getPluginContent($content, ArrayAdapter $params, PluginFetchingContext $ctxt) {
        return new PluginContent($this->getFoldedEntity()->fetchTpl());
    }

}

?>
