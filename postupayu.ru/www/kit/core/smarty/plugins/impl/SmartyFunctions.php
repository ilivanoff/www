<?php

class SmartyFunctions extends AbstractSmartyFunctions {

    const ACTION_EDIT = 'edit';
    const ACTION_COPY = 'copy';
    const ACTION_CONFIRM = 'confirm';
    const ACTION_VIEW = 'view';
    const ACTION_USER = 'user';
    const ACTION_REPLY = 'reply';
    const ACTION_HISTORY = 'history';
    const ACTION_DELETE = 'delete';

    public static function psctrl(array $params) {
        $id = array_get_value_unset('id', $params);
        $class = to_array(array_get_value_unset('class', $params));
        $class[] = 'pscontrols';
        if (!empty($params)) {
            array_remove_keys($params, array_diff(array_keys($params), PsUtil::getClassConsts(__CLASS__, 'ACTION_')));
        }

        return empty($params) ? '' : PSSmarty::template('common/pscontrols.tpl', array('id' => $id, 'class' => PsHtml::classes2string($class), 'actions' => $params))->fetch();
    }

}

?>