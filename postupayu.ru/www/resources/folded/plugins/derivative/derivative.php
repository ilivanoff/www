<?php

/**
 * Плагин - производная.
 */
class PL_derivative extends BasePlugin {

    public function getName() {
        return 'Производная';
    }

    public function getPluginContent($content, ArrayAdapter $params, PluginFetchingContext $ctxt) {
        return new PluginContent($this->getFoldedEntity()->fetchTpl());
    }

}

?>
