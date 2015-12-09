<?php

/**
 * Плагин - тригонометрический круг.
 */
class PL_trigkrug extends BasePlugin {

    public function getName() {
        return 'Тригонометрический круг';
    }

    public function getDescr() {
        return "Приложение разработано для наглядной демонстрации правил пользования единичным кругом, и формул приведения.";
    }

    public function getPluginContent($content, ArrayAdapter $params, PluginFetchingContext $ctxt) {
        return new PluginContent($this->getFoldedEntity()->fetchTpl());
    }

}

?>