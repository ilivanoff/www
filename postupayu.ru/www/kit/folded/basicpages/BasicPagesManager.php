<?php

/**
 * Менеджер ресурсов базовых страниц
 *
 * @author azazello
 */
class BasicPagesManager extends BasicPagesResources {

    /** @return BasicPage */
    public function getPage($ident) {
        return $this->getEntityClassInst(get_file_name($ident));
    }

    /** @return BasicPagesManager */
    public static function inst() {
        return parent::inst();
    }

}

?>
