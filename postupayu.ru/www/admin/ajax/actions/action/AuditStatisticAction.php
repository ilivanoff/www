<?php

class AuditStatisticAction extends AbstractAdminAjaxAction {

    protected function getRequiredParamKeys() {
        return array('action');
    }

    protected function executeImpl(ArrayAdapter $params) {
        $action = $params->str('action');
        $date = $params->int('date');
        $res = array();
        switch ($action) {
            case 'search':
                $res = AdminAuditTools::getAuditStatistic($date);
                break;
            case 'dump':
                $where[] = Query::assocParam('dt_event', $date, true, '<=');
                $order[] = 'dt_event asc';
                $zipDi = AdminTableDump::dumpTable('id_rec', 'ps_audit', $where, $order);
                check_condition($zipDi instanceof DirItem, 'Ошибка снятия дампа. Смотрите лог для деталей.');
                $res['path'] = $zipDi->getAbsPath();
                break;
            case 'load-dumps':
                $res['dumps'] = AP_APAudit::getInstance()->getAuditDumpsInfo();
                break;
            default:
                raise_error("Unknown action: $action");
        }

        return new AjaxSuccess($res);
    }

}

?>
