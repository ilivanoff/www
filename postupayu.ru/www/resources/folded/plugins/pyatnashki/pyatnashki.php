<?php

/**
 * Плагин - Пятнашки.
 */
class PL_pyatnashki extends BasePlugin {

    public function getName() {
        return 'Пятнашки';
    }

    public function getDescr() {
        return "Реализация пятнашек, в которой можно менять размер игрового поля.\n Необходимо собрать головоломку за минимальное количество перемещений и время.";
    }

    public function getPluginContent($content, ArrayAdapter $params, PluginFetchingContext $ctxt) {
        return new PluginContent($this->getFoldedEntity()->fetchTpl());
    }

}

?>
