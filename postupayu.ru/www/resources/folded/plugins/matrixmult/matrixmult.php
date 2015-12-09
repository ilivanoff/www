<?php

/**
 * Плагин - умножение матриц.
 */
class PL_matrixmult extends BasePlugin {

    public function getName() {
        return 'Умножение матриц';
    }

    public function getDescr() {
        return "Приложение наглядно демонстрирует правило умножения матриц.\n Вы можете самостоятельно задать размеры умножаемых матриц и увидеть результат.";
    }

    public function getPluginContent($content, ArrayAdapter $params, PluginFetchingContext $ctxt) {
        return new PluginContent($this->getFoldedEntity()->fetchTpl());
    }

}

?>
