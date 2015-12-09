<?php

/**
 * Методы для работы с аудитом
 *
 * @author azazello
 */
final class AdminAuditBean extends BaseBean {

    /**
     * Метод загражает карту 'код процесса' => 'код действия' => 'кол-во записей'
     * 
     * @return array
     */
    public function getProcessStatistic($dateTo) {
        $where = array();
        if ($dateTo) {
            $where[] = Query::assocParam('dt_event', $dateTo, true, '<=');
        }

        $result = array();
        foreach ($this->getArray(Query::select('id_process, n_action, count(1) as cnt', 'ps_audit', $where, 'id_process, n_action')) as $rec) {
            $result[$rec['id_process']][$rec['n_action']] = $rec['cnt'];
        }
        return $result;
    }

    /** @return AdminAuditBean */
    public static function inst() {
        return parent::inst();
    }

}

?>