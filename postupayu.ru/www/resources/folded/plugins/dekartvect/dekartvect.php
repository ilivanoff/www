<?php

/**
 * Плагин - векторы на декартовой плоскасти.
 */
class PL_dekartvect extends BasePlugin {

    public function getName() {
        return 'Декартова плоскость + векторы ';
    }

    public function getDescr() {
        return "Приложение демонстрирует, как координаты вектора зависят от выбора базиса и как с их помощью вычислить длину вектора или угол между векторами.";
    }

    public function getPluginContent($content, ArrayAdapter $params, PluginFetchingContext $ctxt) {
        return new PluginContent($this->getFoldedEntity()->fetchTpl());
    }

}

?>