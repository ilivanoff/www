<?php

/**
 * Плагин - треугольник Паскаля.
 */
class PL_pascal extends BasePlugin {

    public function getName() {
        return 'Треугольник Паскаля';
    }

    public function getDescr() {
        return "Треугольник Паскаля так прост, что выписать его сможет даже десятилетний ребенок. В то же время он таит в себе неисчерпаемые сокровища и связывает воедино различные аспекты математики, не имеющие, на первый взгляд, между собой ничего общего.\n Приложение позволит наглядно увидеть многие замечательные свойства этого математического чуда.";
    }

    public function getPluginContent($content, ArrayAdapter $params, PluginFetchingContext $ctxt) {
        return new PluginContent($this->getFoldedEntity()->fetchTpl());
    }

}

?>