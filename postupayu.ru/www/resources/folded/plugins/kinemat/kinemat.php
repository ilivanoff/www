<?php

/**
 * Плагин - кинематика точки.
 */
class PL_kinemat extends BasePlugin {

    public function getName() {
        return 'Кинематика точки';
    }

    public function getDescr() {
        return "Приложение демонстрирует, как изменяются проекции вектора перемещения, скорости и ускорения при движении тела. Имеется возможность менять начальные условия движения и видеть, как меняется траектория.";
    }

    public function getPluginContent($content, ArrayAdapter $params, PluginFetchingContext $ctxt) {
        $g = array(
            'Меркурий' => 3.74,
            'Венера' => 8.88,
            'Земля' => 9.81,
            'Луна' => 1.62,
            'Марс' => 3.86,
            'Юпитер' => 23.95,
            'Сатурн' => 10.44,
            'Уран' => 8.86,
            'Нептун' => 11.09,
            'Плутон' => 0.06
        );

        $data['g'] = $g;
        return new PluginContent($this->getFoldedEntity()->fetchTpl($data));
    }

}

?>
