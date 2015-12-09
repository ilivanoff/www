<?php

/**
 * Плагин - поиск слов, буквы в которых перемешаны.
 */
class PL_findwords extends BasePlugin {

    public function getName() {
        return 'Поиск слов, буквы в которых перемешаны';
    }

    public function getPluginContent($content, ArrayAdapter $params, PluginFetchingContext $ctxt) {
        $content = normalize_string($content);
        $strings = explode(' ', $content);
        $data['findwords'] = $strings;
        return new PluginContent($this->getFoldedEntity()->fetchTpl($data));
    }

    /** Видимость для popup */
    public function getPopupVisibility() {
        //Этот плагин можно видеть только с передачей данных в него
        return PopupVis::FALSE;
    }

}

?>