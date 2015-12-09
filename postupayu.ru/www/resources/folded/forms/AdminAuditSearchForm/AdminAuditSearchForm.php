<?php

/**
 * Форма AdminAuditSearchForm
 *
 * @author Admin
 */
class FORM_AdminAuditSearchForm extends BaseSearchForm {

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_AUTHORIZED_AS_ADMIN;
    }

    protected function _construct() {
        parent::_construct();
        $this->setSmartyParam('types', AdminAuditTools::getAuditTypeCombo());
        $this->setSmartyParam('actions', AdminAuditTools::getAuditActionsCombo());
    }

    protected function doSearch(PostArrayAdapter $params) {
        /*
         * Параметры
         */
        $process = $params->int('process');
        $action = $params->int('action');
        $actionParent = $params->int('parent_action');
        $dateFrom = $params->int('date_from');
        $dateTo = $params->int('date_to');

        /*
         * Запрос
         */
        $what[] = 'id_rec';
        $what[] = 'concat(ifnull(id_user, ""), concat("/", id_user_authed)) as user_authed';
        $what[] = 'dt_event';
        $what[] = 'n_action';
        $what[] = 'v_data';
        $what[] = 'b_encoded';

        $where['id_process'] = $process;
        if ($actionParent) {
            $where['id_rec_parent'] = $actionParent;
        }
        if ($action) {
            $where['n_action'] = $action;
        }
        if ($dateFrom) {
            $where[] = Query::assocParam('dt_event', $dateFrom, true, '>=');
        }
        if ($dateTo) {
            $where[] = Query::assocParam('dt_event', $dateTo, true, '<=');
        }

        $order = 'dt_event asc, id_rec asc';

        $limit = 500;
        /*
         * Работа с данными
         */
        $query = Query::select($what, 'ps_audit', $where, null, $order, $limit);
        $result = PSDB::getArray($query);
        foreach ($result as &$row) {
            //Декодируем действие
            $row['n_action'] = BaseAudit::getByCode($process)->decodeAction($row['n_action'], false);
            //Декодируем данные
            $encoded = 1 * $row['b_encoded'];
            if ($encoded) {
                $row['v_data'] = print_r(BaseAudit::decodeData($row['v_data']), true);
            }
            unset($row['b_encoded']);
        }

        $results = new SearchResults($result, $query);
        $results->addSetting('v_data', SearchResults::COL_PRE);
        $results->addSetting('n_action', SearchResults::COL_NOWRAP);
        return $results;
    }

}

?>