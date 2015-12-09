<?php

/**
 * Окно со списком горячих клавиш
 */
class DG_hotkeys extends BaseDialog {

    protected function getWindowTplSmartyParams() {
        throw new Exception('Диалоговое окно ' . self::getIdent() . ' должно строиться на клиенте');
    }

    protected function cacheGroup() {
        return null;
    }

}

?>