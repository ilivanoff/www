<?php

/**
 * Класс для получения общей информации о всех фолдингах, а также для вывода финального лога по всем фолдингам.
 */
final class FoldedResourcesManager extends AbstractSingleton implements Destructable {

    /** @var SimpleDataCache */
    private $CACHE;

    /** @var PsLoggerInterface */
    private $LOGGER;

    /**
     * Метод возвращает сущность фолдинга по заданному коду
     * 
     * @return FoldedEntity Сущность, соответствующая заданному коду
     */
    public function getFoldedEntityByDbCode($code) {
        return $this->CACHE->has($code) ? $this->CACHE->get($code) : $this->CACHE->set($code, Handlers::getInstance()->getFoldedEntityByUnique(FoldingBean::inst()->getUniqueByCode($code)));
    }

    /**
     * Метод возвращает карту: сущность фолдинга=>массив сущностей, от которых она зависит.
     * При этом зависимость сущности от фолдинга будет только в том случае, если сама сущность сохранила об этом информацию в кеше.
     * Не сохраняем построенную карту в переменную класса, так как карта может измениться.
     */
    public function getDependsOnMap() {
        PsProfiler::inst(__CLASS__)->start(__FUNCTION__);
        $result = array();
        /* @var $folding FoldedResources */
        foreach (Handlers::getInstance()->getFoldings() as $folding) {
            if ($folding->isCanDependsOnEntitys()) {
                foreach ($folding->getVisibleIdents() as $ident) {
                    /* @var $ent FoldedEntity */
                    foreach ($folding->getEntitysWeDependsOn($ident) as $parentUq => $ent) {
                        $result[$folding->getUnique($ident)][] = $parentUq;
                    }
                }
            }
        }
        PsProfiler::inst(__CLASS__)->stop();
        return $result;
    }

    /**
     * КАРТА ТАБЛИЦА/ПРЕДСТАВЛЕНИЕ -> СУЩНОСТЬ ФОЛДИНГА, РАБОТАЮЩАЯ С НЕЙ
     */
    private $DB_FOLDING_MAP;

    private function getDbFoldingMap($type, $scope = ENTITY_SCOPE_ALL) {
        if (!is_array($this->DB_FOLDING_MAP[$scope])) {
            $this->DB_FOLDING_MAP[$scope] = array('T' => array(), 'V' => array());

            /* @var $folding FoldedResources */
            foreach (FoldingsStore::inst()->getFoldings($scope) as $folding) {
                if ($folding->isWorkWithTable()) {
                    $T = $folding->getTableName();
                    $V = $folding->getTableView();
                    $this->DB_FOLDING_MAP[$scope]['T'][$T][] = $folding;
                    if ($T != $V) {
                        $this->DB_FOLDING_MAP[$scope]['V'][$V][] = $folding;
                    }
                }
            }

            if ($this->LOGGER->isEnabled()) {
                foreach (array('T' => 'Карта зависимости таблиц:', 'V' => 'Карта зависимости представлений:') as $itype => $title) {
                    $this->LOGGER->info();
                    $this->LOGGER->info("[$scope] [$itype] $title");
                    foreach ($this->DB_FOLDING_MAP[$scope][$itype] as $table => $foldings) {
                        $this->LOGGER->info("\t$table:");
                        foreach ($foldings as $folding) {
                            $this->LOGGER->info("\t\t" . $folding->getUnique());
                        }
                    }
                }
            }
        }
        return array_get_value($type, $this->DB_FOLDING_MAP[$scope]);
    }

    /**
     * DirManager директории, в которой находится основная функциональность для работы с фолдингами
     * @return DirManager
     */
    public function getFoldedDir() {
        return DirManager::inst(__DIR__);
    }

    /** Связь таблицы с фолдингами, которые в ней хрантся */
    public function getTableFoldingsMap($scope = ENTITY_SCOPE_ALL) {
        return $this->getDbFoldingMap('T', $scope);
    }

    /** Связь таблицы с фолдингами, которые в ней хрантся */
    public function getViewsFoldingsMap($scope = ENTITY_SCOPE_ALL) {
        return $this->getDbFoldingMap('V', $scope);
    }

    /**
     * Возвращает все фолдинги, хранимые в указанной таблице.
     */
    public function getTableFoldings($table, $scope = ENTITY_SCOPE_ALL) {
        return array_get_value($table, $this->getTableFoldingsMap($scope), array());
    }

    /**
     * Возвращает все фолдинги, хранимые в указанной таблице.
     */
    public function getViewFoldings($view, $scope = ENTITY_SCOPE_ALL) {
        return array_get_value($view, $this->getViewsFoldingsMap($scope), array());
    }

    /**
     * Возвращает все фолдинги, хранимые в указанной таблице или представлении.
     */
    public function getTableOrViewFoldings($tableOrView) {
        return array_merge($this->getTableFoldings($tableOrView), $this->getViewFoldings($tableOrView));
    }

    /**
     * Функция для записи общих логов от имени разных фолдингов, чтобы увидеть в одном месте,
     * какие функции вызываются.
     * 
     * @param FoldedResources $folded - фолдинг, от имени которого пишется лог
     * @param type $msg - сообщение
     */
    public static function info(FoldedResources $folded, $msg) {
        if (self::inst()->LOGGER->isEnabled()) {
            self::inst()->LOGGER->info('[' . $folded->getUnique() . '] ' . $msg);
        }
    }

    /**
     * Действия, которые вконце будут отлогированы
     */
    //Действия над фолдингами

    const ACTION_FOLDING_ALL_CHECKED = 'Фолдинги, для которых все сущности проверены на изменеие';
    const ACTION_FOLDING_ONCE_CHENGED = 'Фолдинги, для которых был вызван onFoldingChanged';

    //Действия над сущностями
    const ACTION_ENTITY_CHECK_CHANGED = 'Сущности, проверенные на изменение';
    const ACTION_ENTITY_CHANGED_DB = 'Список изменённых в БД';
    const ACTION_ENTITY_CHANGED = 'Список изменённых сущностей';
    const ACTION_ENTITY_INST_CREATED = 'Список созданных экземпляров классов';
    const ACTION_ENTITY_FETCH_REQUESTD = 'Сущности, для которых запрошен фетчинг шаблона';
    const ACTION_ENTITY_FETCH_DONE = 'Сущности, для которых фактически выполнен фетчинг шаблона';

    private $ACTIONS = array();

    public static function onEntityAction($action, FoldedResources $folding, $ident = null, $msg = null) {
        if (self::inst()->LOGGER->isEnabled()) {
            self::inst()->ACTIONS[$action][] = array($folding->getUnique($ident), $msg);
        }
    }

    /**
     * В процессе закрытия данного класса мы напишем полный список изменённых сущностей
     */
    public function onDestruct() {
        foreach (array('ACTION_FOLDING_' => 'Фолдинги', 'ACTION_ENTITY_' => 'Сущности') as $CONST_PREFIX => $name) {
            $this->LOGGER->infoBox($name);
            foreach (PsUtil::getClassConsts($this, $CONST_PREFIX) as $action) {
                $idents = array_get_value($action, $this->ACTIONS, array());
                $count = count($idents);

                $this->LOGGER->info();
                $this->LOGGER->info($action . ':');

                if ($count > 0) {
                    for ($i = 0; $i < $count; $i++) {
                        $this->LOGGER->info("\t" . (1 + $i) . '. ' . $idents[$i][0] . ($idents[$i][1] ? ' [' . $idents[$i][1] . ']' : ''));
                    }
                } else {
                    $this->LOGGER->info("\t -- Нет --");
                }
            }
        }

        /**
         * Распечатаем карту зависимости сущностей фолдинга.
         * Операция настолько тяжёлая, что в режиме ajax также будем избегать её выполнение.
         */
        if (PsDefines::isDevmode() && !PageContext::inst()->isAjax()) {
            $this->LOGGER->infoBox('Карта зависимости сущностей фолдингов:');
            foreach ($this->getDependsOnMap() as $who => $fromWhoArr) {
                $this->LOGGER->info("\t$who:");
                foreach ($fromWhoArr as $fromWho) {
                    $this->LOGGER->info("\t\t$fromWho");
                }
            }
        }
    }

    /** @return FoldedResourcesManager */
    public static function inst() {
        return parent::inst();
    }

    protected function __construct() {
        $this->CACHE = new SimpleDataCache();
        $this->LOGGER = PsLogger::inst(__CLASS__);
        if ($this->LOGGER->isEnabled()) {
            PsShotdownSdk::registerDestructable($this, PsShotdownSdk::FoldedResourcesManager);
        }
    }

}

?>