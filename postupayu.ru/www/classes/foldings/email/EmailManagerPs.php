<?php

/**
 * Ресурсы фолдингов для отправки электронных писем
 *
 * @author azazello
 */
class EmailManagerPs extends EmailResourcesPs/* implements DatabasedFolding */ {

    /** @return EmailManagerPs */
    public static function inst() {
        return parent::inst();
    }

    public function dbRec4Entity($ident) {
        return array();
    }

    public function foldingTable() {
        return 'ps_lib_item.ident.grup';
    }

}

?>