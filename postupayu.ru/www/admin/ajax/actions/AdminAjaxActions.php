<?php

/**
 * Класс, возвращающий классы-реализации для админских Ajax-действий
 */
class AdminAjaxActions {

    /** @return AbstractAdminAjaxAction */
    public static function getAction() {
        return Classes::getClassInstance(__DIR__, 'action', RequestArrayAdapter::inst()->str(AJAX_ACTION_PARAM), 'AbstractAdminAjaxAction');
    }

}

?>