<?php

/**
 * Утилиты для работы с аудитом
 *
 * @author azazello
 */
final class AdminAuditTools {

    /**
     * Все типы аудитов для формы поиска
     */
    public static function getAuditTypeCombo() {
        $data = array();
        /* @var $audit BaseAudit */
        foreach (BaseAudit::getAll() as $audit) {
            $data[] = PsHtml::comboOption($audit->getProcessCode(), $audit->getClass() . '&nbsp;&nbsp;(' . $audit->getDescription() . ')');
        }
        return $data;
    }

    /**
     * Все типы аудитов для формы поиска
     */
    public static function getAuditActionsCombo() {
        $data = array();
        /* @var $audit BaseAudit */
        foreach (BaseAudit::getAll() as $audit) {
            foreach ($audit->getActions() as $name => $code) {
                $data[] = PsHtml::comboOption($code, $name . "&nbsp;&nbsp;($code)", array('data' => array('process' => $audit->getProcessCode())));
            }
        }
        return $data;
    }

    /**
     * метод загружает кол-во записей для каждого аудита
     */
    public static function getAuditStatistic($dateTo) {
        $RESULT = array();
        $statistic = AdminAuditBean::inst()->getProcessStatistic($dateTo);
        /* @var $audit BaseAudit */
        foreach (BaseAudit::getAll() as $code => $audit) {
            foreach ($audit->getActions() as $actionName => $actionCode) {
                $RESULT[] = array('name' => $audit->getClass(), 'action' => "$actionName ($actionCode)", 'cnt' => array_get_value_in(array($code, $actionCode), $statistic));
            }
        }
        return $RESULT;
    }

}

?>