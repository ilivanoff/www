<?php

/**
 * Класс для работы с базой данных.
 */
class DbBean extends BaseBean {
    //Типы сущностей, которые могут изменяться

    const CHANGE_TABLE = 'T';
    const CHANGE_VIEW = 'V';
    const CHANGE_FOLD_ENT = 'F';

    /**
     * Загружает список всех изменений, произошедших в базе.
     */
    public function getChangedEntitys() {
        PsProfiler::inst(__CLASS__)->start(__FUNCTION__);

        //Первым делом проверим состояние представлений, возможно они изменены
        $this->update('call checkViewsState()');

        //Загрузим все изменённые сущности
        $CHANGED = $this->getArray('select v_entity, v_type from ps_db_changes order by v_type');
        if ($CHANGED) {
            //Очищаем историю изменений
            $this->update('delete from ps_db_changes');
        }

        PsProfiler::inst(__CLASS__)->stop();

        return $CHANGED;
    }

    /**
     * Загружает список всех представений и таблиц базы данных. Восновном используется для
     * маппинга групп кеширования на сущности БД.
     */
    public function getAllTablesAndViews() {
        return $this->getValues('SELECT TABLE_NAME as value FROM INFORMATION_SCHEMA.TABLES WHERE table_schema=DATABASE() and TABLE_TYPE=? union all SELECT TABLE_NAME as value FROM INFORMATION_SCHEMA.VIEWS WHERE TABLE_SCHEMA=DATABASE()', 'BASE TABLE');
    }

    /*
     * СИНГЛТОН
     */

    /** @return DbBean */
    public static function inst() {
        return parent::inst();
    }

}

?>
