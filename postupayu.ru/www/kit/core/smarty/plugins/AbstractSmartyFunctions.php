<?php

/**
 * Базовый класс для классов SmartyBlocks, SmartyFunctions, SmartyModifiers.
 * Все [public static] методы, определённые в классах-наследниках, также являются плагинами смарти.
 * Их можно вынести в соответствующие классы, чтобы вызывать также и из php кода.
 * Тип плагина отпределим по названию класса-наследника, отрезав префикс 'Smarty' и суффикс 's'.
 */
abstract class AbstractSmartyFunctions extends AbstractSmartyPlugin {

    protected function getPlugins() {
        $type = strtolower(cut_string_end(cut_string_start($this->CLASS, 'Smarty'), 's'));

        $result = array();
        foreach (PsUtil::getClassMethods($this->CLASS, true, true, null, true) as $method) {
            $result[$method] = $type;
        }
        return $result;
    }

}

?>