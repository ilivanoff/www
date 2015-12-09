<?php

/**
 * Класс, возвращающий классы-реализации для Ajax-действий
 */
class AjaxActions {

    /** @return AbstractAjaxAction */
    public static function getAction() {
        return Classes::getClassInstance(__DIR__, 'action', RequestArrayAdapter::inst()->str(AJAX_ACTION_PARAM), 'AbstractAjaxAction');
    }

}

?>
