<?php

/**
 * Плагин - Тригонометрический калькулятор.
 */
class PL_trigcalc extends BasePlugin {

    public function getName() {
        return 'Тригонометрический калькулятор';
    }

    public function getDescr() {
        return "Приложение позволит легко и быстро перевести градусы в радианы и обратно.\n Также можно легко расчитать любую тригонометрическую функцию.";
    }

    public function getPluginContent($content, ArrayAdapter $params, PluginFetchingContext $ctxt) {
        return new PluginContent($this->getFoldedEntity()->fetchTpl());
    }

    public function getPopupVisibility() {
        return PopupVis::TRUE;
    }

}

?>
