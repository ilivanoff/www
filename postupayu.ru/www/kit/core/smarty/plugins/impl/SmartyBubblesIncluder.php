<?php

class SmartyBubblesIncluder extends AbstractSmartyPlugin {

    private function insert($fname, ArrayAdapter $params, $text) {
        //Идентификатор
        $ident = $params->get('ident');
        //Текст
        $text = $text == '.' ? null : $text;
        //Имплементация доджна вернуть ссылку для показа bubble
        return Handlers::getInstance()->getFoldingBySmartyPrefix($fname)->getBubbleHref($ident, $text, $params);
    }

    /**
     * Основной метод
     */
    protected function do_block($tagName, $params, $content, Smarty_Internal_Template $smarty) {
        if ($content) {
            return $this->insert($tagName, ArrayAdapter::inst($params), trim($content));
        }
    }

    private function registerPluginImpl(array &$result, $prefix) {
        $result[$prefix] = Smarty::PLUGIN_BLOCK; //Элемент библиотеки
    }

    protected function getPlugins() {
        $result = array();
        /* @var $manager FoldedResources */
        foreach (Handlers::getInstance()->getBubbles() as $manager) {
            $this->registerPluginImpl($result, $manager->getSmartyPrefix());
        }
        return $result;
    }

}

?>