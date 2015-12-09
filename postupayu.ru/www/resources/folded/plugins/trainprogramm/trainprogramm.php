<?php

/**
 * Плагин - тренировочная программа.
 */
class PL_trainprogramm extends BasePlugin {

    public function getName() {
        return 'Тренировочная программа';
    }

    public function getDescr() {
        return "Приложение позволит составить свою тренировочную программу на основе огромного списка упражнений, которые содержат детальные описания. Просто включайте их в программу и используйте.";
    }

    public function getPluginContent($content, ArrayAdapter $params, PluginFetchingContext $ctxt) {
        return new PluginContent($this->getFoldedEntity()->fetchTpl());
    }

}

?>