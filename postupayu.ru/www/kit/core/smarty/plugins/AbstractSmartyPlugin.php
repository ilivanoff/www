<?php

abstract class AbstractSmartyPlugin {

    /** @var PsLoggerInterface */
    protected $LOGGER;

    /** @var PsLoggerInterface */
    protected $CLASS;

    public final function __construct() {
        $this->CLASS = get_called_class();
        $this->LOGGER = PsLogger::inst($this->CLASS);
    }

    //Массив: $tagName=>$pluginType
    protected abstract function getPlugins();

    private function raiseNotRealised($__CLASS__, $__FUNCTION__, $tagName) {
        check_condition(false, "Не реализована функиця $__CLASS__->$__FUNCTION__, при этом она вызвана для тега $tagName");
    }

    protected function do_block($tagName, $params, $content, Smarty_Internal_Template $smarty) {
        $this->raiseNotRealised(__CLASS__, __FUNCTION__, tagName);
    }

    protected function do_function($tagName, $params, Smarty_Internal_Template $smarty) {
        $this->raiseNotRealised(__CLASS__, __FUNCTION__, tagName);
    }

    protected function do_modifier($tagName) {
        $this->raiseNotRealised(__CLASS__, __FUNCTION__, tagName);
    }

    /*
     * private
     */

    public function __call($name, $arguments) {
        $tokens = explode('_', $name, 3); //0-do, 1-$pluginType, 2-$tagName
        $pluginType = $tokens[1];
        $tagName = $tokens[2];

        if (method_exists($this, $tagName)) {
            //Метод, совпадающий с названием тега, есть в классе. Просто вызовем его.
            return call_user_func_array(array($this, $tagName), $arguments);
        } else {
            //Метода нет, вызываем do_...
            //К параметрам в начало добавим название тега.
            array_unshift($arguments, $tagName);
            return call_user_func_array(array($this, "do_$pluginType"), $arguments);
        }
    }

    /**
     * Основной метод, вызываемый для регистрации плагинов, предоставляемых данным классом для выполнения смарти-функций
     */
    public final function registerPlugins(Smarty $smarty) {
        $this->LOGGER->info('Регистрируем плагины класса [{}]', $this->CLASS);
        foreach ($this->getPlugins() as $tagName => $pluginType) {
            PSSmartyTools::checkFunctionType($pluginType);
            if (array_key_exists($pluginType, $smarty->registered_plugins) && array_key_exists($tagName, $smarty->registered_plugins[$pluginType])) {
                $this->LOGGER->info('Не зарeгистрирован плагин [{}]', 'smarty_' . $pluginType . '_' . $tagName);
            } else {
                $smarty->registerPlugin($pluginType, $tagName, array($this, 'do_' . $pluginType . '_' . $tagName));
                $this->LOGGER->info('Зарeгистрирован плагин [{}]', 'smarty_' . $pluginType . '_' . $tagName);
            }
        }
    }

}

?>