<?php

class FoldingAction extends AbstractAdminAjaxAction {

    protected function getRequiredParamKeys() {
        return array('action', 'unique');
    }

    protected function executeImpl(ArrayAdapter $params) {
        $action = $params->str('action');
        $unique = $params->str('unique');
        $folding = Handlers::getInstance()->getFoldingByUnique($unique);

        $res = 'OK';

        switch ($action) {
            case 'save_list':
                $list = $params->str('list');
                $data = $params->arr('data');
                $folding->saveList($list, $data);
                break;
            default:
                return 'Неизвестное действие: ' . $action;
        }

        return new AjaxSuccess($res);
    }

}

?>
