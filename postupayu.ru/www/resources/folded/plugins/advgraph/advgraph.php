<?php

/**
 * Плагин - AdvancedGrapher.
 */
class PL_advgraph extends BasePlugin {

    public function getName() {
        return 'Построение графиков';
    }

    public function getDescr() {
        return 'Приложение позволяет строить различные графики, касательные к этим графикам, а также области криволинейных трапеций (интегралы).';
    }

    public function getPluginContent($content, ArrayAdapter $params, PluginFetchingContext $ctxt) {
        return new PluginContent($this->getFoldedEntity()->fetchTpl());
    }

    public function getPopupVisibility() {
        return PopupVis::TRUE_DEFAULT;
    }

}

?>