<?php

class SmartyPanelsInclider extends AbstractSmartyPlugin {

    const MODIFIER_SUFFIX = 'panel';

    protected function do_modifier($tagName, $panelName) {
        $smartyPrefix = cut_string_end($tagName, self::MODIFIER_SUFFIX);
        echo Handlers::getInstance()->getFoldingBySmartyPrefix($smartyPrefix)->includePanel($panelName);
    }

    /**
     * Основной метод
     */
    protected function getPlugins() {
        $result = array();
        /* @var $manager FoldedResources */
        foreach (Handlers::getInstance()->getPanelProviders() as $manager) {
            $prefix = $manager->getSmartyPrefix();
            $result[$prefix . self::MODIFIER_SUFFIX] = Smarty::PLUGIN_MODIFIER; //Модификатор
        }
        return $result;
    }

}

?>