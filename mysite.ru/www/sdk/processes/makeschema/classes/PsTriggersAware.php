<?php

/**
 * Класс, знающий всё о том, какие триггеры должны быть добавлены на таблицы.
 * У него две основных задачи:
 * 1. сформировать список таблиц, на которые должны быть добавлены триггеры
 * 2. вернуть вызов процедуры для реакции на связку таблица+действие
 *
 * @author azazello
 */
final class PsTriggersAware {

    const ACTION_INSERT = 'INSERT';
    const ACTION_UPDATE = 'UPDATE';
    const ACTION_DELETE = 'DELETE';

    /**
     * Загружает все таблицы, к которым может быть привязан кеш
     */
    private static function getTablesWithCache($scope) {
        return to_array(ConfigIni::getPropCheckType(ConfigIni::GROUP_TABLE_CHANGE_TRIGGERS, 'tables', array(PsConst::PHP_TYPE_NULL, PsConst::PHP_TYPE_ARRAY), $scope));
    }

    /**
     * Загружает все таблицы, к которым может быть привязан кеш
     */
    private static function getTables2Foldings($scope) {
        return FoldedResourcesManager::inst()->getTableFoldingsMap($scope);
    }

    /**
     * Все возможные действия над таблицей
     */
    public static function getActions() {
        return PsUtil::getClassConsts(__CLASS__, 'ACTION_');
    }

    /**
     * Все таблицы, на которые должен быть повешен триггер
     */
    public static function getTriggeredTables($scope = ENTITY_SCOPE_ALL) {
        /**
         * Кешируемые таблицы
         */
        $tables2cache = self::getTablesWithCache($scope);

        /**
         * Таблицы, в которых хранятся фолдинги
         */
        $tables2foldings = array_keys(self::getTables2Foldings($scope));

        /**
         * Все таблицы
         */
        return array_unique(array_merge($tables2foldings, $tables2cache));
    }

    /**
     * Метод возвращает список действий, которые должны быть выполнены в рамках триггера.
     * Под действием понимается вызов процедуры.
     * 
     * @return null|string
     */
    public static function getTriggerActions($table, $scope, $action) {
        $result = array();

        $TABLE_WITH_CACHE = self::getTablesWithCache($scope == ENTITY_SCOPE_PROJ ? ENTITY_SCOPE_ALL : $scope);
        $TABLE2FOLDINGS = self::getTables2Foldings($scope == ENTITY_SCOPE_PROJ ? ENTITY_SCOPE_ALL : $scope);

        if (in_array($table, $TABLE_WITH_CACHE) || array_key_exists($table, $TABLE2FOLDINGS)) {
            $result[] = "CALL onDbChange('$table', '" . DbBean::CHANGE_TABLE . "')";
        }

        if ($action == self::ACTION_UPDATE && array_key_exists($table, $TABLE2FOLDINGS)) {
            /* @var $folding FoldedResources */
            foreach ($TABLE2FOLDINGS[$table] as $folding) {
                $columnIdent = $folding->getTableColumnIdent();
                $columnStype = $folding->getTableColumnStype();

                if ($columnStype) {
                    //Если в таблице есть столбец с подтипом фолдинга, то достаточно добавить один триггер
                    $ftype = $folding->getFoldingType();
                    $result[] = "CALL onDbChange(CONCAT('$ftype-', NEW.$columnStype, '-', NEW.$columnIdent), '" . DbBean::CHANGE_FOLD_ENT . "')";
                } else {
                    $unique = $folding->getUnique();
                    $result[] = "CALL onDbChange(CONCAT('$unique-', NEW.$columnIdent), '" . DbBean::CHANGE_FOLD_ENT . "')";
                }
            }
        }


        /**
         * Небольшая обработка
         */
        $fetched = array();
        foreach ($result as $line) {
            $fetched[] = "\t" . ensure_ends_with(trim($line), ';');
        }

        /*
         * Если мы собираем действия для проекта и они совпадают с действиями для SDK - триггер не выкладываем
         */
        if ($scope == ENTITY_SCOPE_PROJ) {
            $sdkActions = self::getTriggerActions($table, ENTITY_SCOPE_SDK, $action);
            if (simple_hash($fetched) == simple_hash($sdkActions)) {
                return null; //---
            }
        }

        return $fetched;
    }

}

?>
