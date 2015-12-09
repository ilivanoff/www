<?php

/**
 * Базовый класс для всех фолдингов
 */
abstract class FoldedResources extends AbstractSingleton {

    const RTYPE_JS = 'js';
    const RTYPE_CSS = 'css';
    const RTYPE_PCSS = 'print_css';
    const RTYPE_PHP = 'php';
    const RTYPE_TPL = 'tpl';
    const RTYPE_TXT = 'txt';

    //Директория с информациооными шаблонами
    const INFO_PATTERNS = 'tpl';

    //Название шаблона
    const PATTERN_NAME = '!PATTERN';

    //Уникальный идентификатор фолдинга. "Собирается" из типа и подтипа фолдинга.
    private $UNIQUE;

    /** Класс */
    protected $CLASS;

    /** @var PsLoggerInterface */
    protected $LOGGER;

    /** @var PsProfilerInterface */
    protected $PROFILER;

    /** @var SimpleDataCache */
    private $INSTS_CACHE;

    /** Название таблицы, хранящей сущности фолдинга */
    private $TABLE;

    /** Название вьюхи, хранящей видимые сущности фолдинга */
    private $TABLE_VIEW;

    /** Название столбца в таблице, хранящей идентификатор фолдинга */
    private $TABLE_COLUMN_IDENT;

    /** Название столбца в таблице, хранящей подтип фолдинга. Считаем, что все фолдинги одного типа хранятся в одной таблице. */
    private $TABLE_COLUMN_STYPE;

    /** Префикс для Smarty-функций, чтобы можно было мгновенно находить фолдинг. */
    private $SMARTY_PREFIX;

    /** Префикс для классов, например IP_. Если забора с типом ресурсов - PHP не ведётся, то - null */
    private $CLASS_PREFIX;

    /** Базовый путь к классам, для максимально быстрого получения classpath */
    private $CLASS_PATH_BASE;

    /** текстовое описание фолдинга */
    private $TO_STRING;

    /** Признак, относится ли данный фолдинг к SDK */
    private $IS_SDK = null;

    /** Ремап типа на расширение */
    private static $TYPE2EXT = array(self::RTYPE_PCSS => 'print.css');

    /** Подключаемые типы ресурсов */
    private $RESOURCE_TYPES_LINKED = array(self::RTYPE_JS, self::RTYPE_CSS, self::RTYPE_PCSS);

    /** Типы ресурсов, по которым будет происходить проверка - изменилась ли сущность */
    private $RESOURCE_TYPES_CHECK_CHANGE = array(self::RTYPE_TPL, self::RTYPE_PHP, self::RTYPE_TXT);

    /** Допустимые типы ресурсов */
    protected $RESOURCE_TYPES_ALLOWED = array(self::RTYPE_JS, self::RTYPE_CSS, self::RTYPE_PCSS, self::RTYPE_PHP, self::RTYPE_TPL);

    //Тип foldings (pp, pl, post...)
    public abstract function getFoldingType();

    //Подтип foldings (null, is, tr...)
    public abstract function getFoldingSubType();

    //Группа foldings (путь к папке от директории foldings, в которой лежат ресурсы фолдинга)
    public abstract function getFoldingGroup();

    //Название сущности
    public abstract function getEntityName();

    //Метод возвращает идентификатор для вновь создаваемой записи фолдинга. Используется для первичного наполнения форм и т.д.
    public function getNextEntityIdent() {
        $name = $this->getFoldingType() . trim($this->getFoldingSubType()) . 'new';
        $ident = $name;
        for ($idx = 0; $this->existsEntity($ident); $idx++) {
            $ident = $name . $idx;
        }
        return $ident;
    }

    /**
     * Функция возвращает префикс для смарти-функций, соответствующий данному фолдингу.
     * Например функция trpostimg - сразу указывает на фолдинг post-tr.
     * Данный префикс являет собой просто слияние подтипа + типа фолдинга.
     */
    public function getSmartyPrefix() {
        return $this->SMARTY_PREFIX;
    }

    /**
     * Функция возвращает префикс для классов данного фолдинга, например IP_
     */
    public function getClassPrefix() {
        return $this->CLASS_PREFIX;
    }

    //Название класса по его идентификатору (calculator->PP_calculator)
    protected function ident2className($ident) {
        return $this->CLASS_PREFIX . $ident;
    }

    //Название идентификатор по названию класса (PP_calculator -> calculator)
    private function className2ident($className) {
        return cut_string_start($className, $this->CLASS_PREFIX);
    }

    //Метод проверяет, может ли переданная последовательность служиьт префиксом класса.
    //Она должна сосотоять из больших букв и заканчиваться подчёркиванием, например: PL_
    private static function isValidClassPrefix($prefix) {
        return $prefix && preg_match('/[A-Z]+\_/', $prefix, $matches) == 1 && $matches[0] === $prefix;
    }

    //Метод извлекат префикс из имени класса
    public static function extractPrefixFromClass($className) {
        $tokens = explode('_', trim($className), 3);
        $prefix = count($tokens) == 2 ? $tokens[0] . '_' : null;
        return self::isValidClassPrefix($prefix) ? $prefix : null;
    }

    //Метод извлекат идентификатор сущности из имени класса. Конечно мы проверим, валиден ли префикс класса
    public static function extractIdentFormClass($className) {
        $tokens = explode('_', trim($className), 3);
        $prefix = count($tokens) == 2 ? $tokens[0] . '_' : null;
        return self::isValidClassPrefix($prefix) ? $tokens[1] : null;
    }

    //Предпросмотр сущности фолдинга при редактировании
    public function getFoldedEntityPreview($ident) {
        return array('info' => '', 'content' => '');
    }

    //Дополнительный список файлов, которые будут проверены на изменение, совместно с $RESOURCE_TYPES_CHECK_CHANGE
    protected function getDirItems4CheckChanged($ident) {
        return array();
    }

    /**
     * Методы различных проверок
     */
    public function isIt($type, $subtype = null) {
        return $this->getFoldingType() == $type && (!$this->getFoldingSubType() || $this->getFoldingSubType() == $subtype);
    }

    /**
     * Проверка - входит ли фолдинг в SDK.
     * Важно! В конструкторе такую проверку делать нельзя, так как экземпляр фолдингов создаётся в хранилище фолдингов,
     * которое уже и знает, к чему относится фолдинг - к проекту или к SDK.
     * 
     * @return bool
     */
    public final function isSdk() {
        return $this->IS_SDK === null ? $this->IS_SDK = FoldingsStore::inst()->isSdkFolding($this) : $this->IS_SDK;
    }

    public function isItByType($type) {
        return $this->getFoldingType() == $type;
    }

    public function hasSubType() {
        return !!$this->getFoldingSubType();
    }

    public static function unique($type, $subtype = null, $ident = null) {
        return $type . ($subtype ? "-$subtype" : '') . ($ident ? "-$ident" : '');
    }

    private static function smartyPrefix($type, $subtype = null) {
        return trim($subtype) . $type;
    }

    //Уникальный идентификатор фолдинга, либо сущности внутри фолдинга (если передана)
    public function getUnique($ident = null) {
        return $this->UNIQUE . ($ident ? "-$ident" : '');
    }

    //Текстовое описание фолдинга или сущности фолдинга
    public final function getTextDescr($ident = null) {
        return $this->CLASS . "[{$this->getUnique($ident)}] ({$this->getEntityName()})";
    }

    /*
     * **************************
     *       ПРОФИЛИРОВАНИЕ
     * **************************
     */

    protected final function profilerStart($__FUNCTION__) {
        PsProfiler::inst('Folding')->start($this->CLASS . "[$this->UNIQUE]->" . $__FUNCTION__);
    }

    protected final function profilerStop($save = true) {
        PsProfiler::inst('Folding')->stop($save);
    }

    /*
     * **************************
     *            КЕШИ
     * **************************
     */

    //Константы - префиксы служебных кешей

    const CACHE_CHANGE_PROCESS = 'CHANGE_PROCESSED';
    const CACHE_INFO_TPL = 'TPL_';
    const CACHE_TXT_PARAMS = 'TXTPARAMS';
    const CACHE_DEPEND_ENTS = 'DEPEND_ENTS';

    /**
     * В качестве подписи кешей сущности фолдинга используется время самого последнего 
     * изменённого файла из тех ресурсов, которые включены в RESOURCE_TYPES_CHECK_CHANGE.
     */
    private $OLDEST = array();

    private function getOldestResourceFile($ident) {
        if (array_key_exists($ident, $this->OLDEST)) {
            return $this->OLDEST[$ident];
        }

        $this->assertExistsEntity($ident);
        $this->LOGGER->info("Loading oldest resource file for entity [$ident].");

        $this->PROFILER->start(__FUNCTION__);

        //Строим полный список сущностей, которые будут проверены на дату последнего изменения
        $items[] = to_array($this->getDirItems4CheckChanged($ident));
        foreach ($this->RESOURCE_TYPES_CHECK_CHANGE as $type) {
            $items[] = $this->getResourceDi($ident, $type);
        }
        //Включим в список преверяемых сущностей все информационные шаблоны
        $items[] = $this->getInfoDiList($ident);
        //Если мы работаем с обложками - проверим и их
        if ($this->isImagesFactoryEnabled()) {
            $items[] = $this->getCoverOriginal($ident);
        }

        $oldest = null;
        foreach ($items as $item) {
            /** @var $di DirItem */
            foreach (to_array($item) as $di) {
                $time = $di->getModificationTime();
                if ($time && (!$oldest || $time > $oldest)) {
                    $oldest = $time;
                    $this->LOGGER->info("Resource file [{$di->getRelPath()}] mtime: $time, oldest: $oldest.");
                } else {
                    $this->LOGGER->info("Resource file [{$di->getRelPath()}] mtime: $time.");
                }
            }
        }

        $this->PROFILER->stop();

        return $this->OLDEST[$ident] = $oldest;
    }

    /** Кеш группы фолдинга. Всегда одинаков для сущности, чтобы мы могли зачистить все кеши по данному фолдингу */
    private function cacheGroup($ident) {
        return 'FOLDING-' . $this->getUnique($ident);
    }

    /**
     * Метод проверяет, можно ли использовать кеш.
     * 
     * @param string $ident - идентификатор сущности. Если она не видна, то кеш использовать нельзя.
     * @param string $cacheId - ключ кеширования. Если он начинается на префикс, зарегистрированный в данном классе, то кеш можно использовать (служебный кеш).
     * @return bool
     */
    private function isCanUseCache($ident = null, $cacheId = null) {
        if ($ident && !$this->isVisibleEntity($ident)) {
            //Сущность не видна
            return false;
        }
        if ($this instanceof StorableFolding) {
            return true;
        }
        if (!!$cacheId && starts_with($cacheId, PsUtil::getClassConsts(__CLASS__, 'CACHE_'))) {
            return true;
        }
        return false;
    }

    /**
     * Метод проверяет, может ли данный класс в принципе использовать кеши.
     */
    private function assertClassCanUseCache($cacheId = null) {
        check_condition($this->isCanUseCache(null, $cacheId), "Фолдинг $this не может использовать кеши. Информация по ключу [$cacheId] не будет сохранена.");
    }

    /**
     * Загрузка из кеша
     */
    public function getFromFoldedCache($ident, $cacheId, $checkKeys = null) {
        $this->assertExistsEntity($ident);
        $this->assertClassCanUseCache($cacheId);

        if (!$this->isCanUseCache($ident, $cacheId)) {
            return null; //Для данной сущности нельзя использовать кеши
        }

        /*
         * Хоть мы и подписываем все кеши датой модификации самого старого файла, тем не менее 
         * будем выполнять checkEntityChanged, так как в случае изменения сущности нужно обновить 
         * и всё остальное - сбросить спрайты и т.д.
         */
        $this->checkEntityChanged($ident);
        $sign = $this->getOldestResourceFile($ident);
        $groupId = $this->cacheGroup($ident);
        return PSCache::inst()->getFromCache($cacheId, $groupId, $checkKeys, $sign);
    }

    /**
     * Сохранение в кеш
     */
    public function saveToFoldedCache($object, $ident, $cacheId) {
        $this->assertExistsEntity($ident);
        $this->assertClassCanUseCache($cacheId);

        if (!$this->isCanUseCache($ident, $cacheId)) {
            return $object; //Для данной сущности нельзя использовать кеши
        }

        $sign = $this->getOldestResourceFile($ident);
        $groupId = $this->cacheGroup($ident);
        return PSCache::inst()->saveToCache($object, $cacheId, $groupId, $sign);
    }

    /**
     * Очистка кеша
     */
    private function cleanFoldedCache($ident) {
        if ($this->isCanUseCache($ident)) {
            PSCache::inst()->clean($this->cacheGroup($ident));
        }
    }

    /*
     * **************************
     *   ЗАВИСИМОСТЬ СУЩНОСТЕЙ
     * **************************
     */

    /**
     * Метод возвращает признак - могут ли сущности данного фолдинга зависеть от сущностей других фолдингов.
     * Это возможно, если данный фолдинг наследует интерфейс StorableFolding и, соответственно, может сохранять своё состояние (использовать кеши).
     * 
     * @param string $ident - идентификатор фолдинга. Если передан, то будет проверенно именно для него.
     */
    public final function isCanDependsOnEntitys($ident = null) {
        if ($ident && !$this->isVisibleEntity($ident)) {
            return false;
        }
        return $this instanceof StorableFolding;
    }

    /**
     * Метод отмечает, что сущность данного фолдинга зависит от сущности другого фолдинга.
     * Будет выполнена проверка на то, что сущность не зависит сама от себя.
     * При этом мы не запрещаем сущностям одного фолждинга зависеть друг от друга.
     * 
     * Будем требовать именно передачи FoldedEntity, чтобы не заботиться о проверке существования сущности.
     * 
     * @param string $ident - идентификатор сущности данного фолдинга
     * @param FoldedEntity $entity - сущность, от которой зависит сущность данного фолдинга
     */
    private $WE_DEPENDS_ON_CACHE = array();

    public function setDependsOnEntity($ident, FoldedEntity $entity) {
        if (!$this->isCanDependsOnEntitys($ident)) {
            //Мы не используем кеш или сущность не видна, поэтому - не зависим от других сущностей фолдингов
            return; //---
        }

        if (!$entity->getFolding()->isVisibleEntity($entity->getIdent())) {
            //Мы обнаружили, что в видимой сущности используется невидимая
            raise_error('Visible entity [' . $this->getUnique() . '] cannot depends on invisible entity [' . $entity->getUnique() . ']');
        }

        if ($this->getUnique($ident) == $entity->getUnique()) {
            //Сущность не может зависеть от самой себя
            return; //---
        }
        $depends = $this->getFromFoldedCache($ident, self::CACHE_DEPEND_ENTS);
        $depends = to_array($depends);
        if (in_array($entity->getUnique(), $depends)) {
            //Мы уже отметили, что зависим от данной сущности
            return; //---
        }

        $this->LOGGER->info("Entity [$ident] is depends on entity [$entity].");

        $depends[] = $entity->getUnique();
        $this->saveToFoldedCache($depends, $ident, self::CACHE_DEPEND_ENTS);

        unset($this->WE_DEPENDS_ON_CACHE[$ident]);
    }

    /**
     * Возвращает список сущностей, от которых зависит сущность данного фолдинга.
     * Метод используется для проверки сущности на изменение.
     * 
     * !МЕТОД ВЫЗЫВАЕТСЯ ОЧЕНЬ ЧАСТО!
     * Дело в том, что после очистки кеша все сущности "считают", что они изменились
     * и начинают пробегать по всем сущностям фолдингов, оповещая об изменении,
     * то есть вызывать функцию {@link #getEntitysDependableFromUs}.
     * 
     * Поэтому нам нужно работать максимально быстро, используем кеширование на уровне класса.
     * 
     * unique => FoldedEntity
     */
    public final function getEntitysWeDependsOn($selfEntityIdent) {
        if (!$this->isCanDependsOnEntitys()) {
            return array();
        }
        if (!array_key_exists($selfEntityIdent, $this->WE_DEPENDS_ON_CACHE)) {
            $cached = to_array($this->getFromFoldedCache($selfEntityIdent, self::CACHE_DEPEND_ENTS));

            $this->WE_DEPENDS_ON_CACHE[$selfEntityIdent] = array();
            foreach ($cached as $parentEntityUnique) {
                $this->WE_DEPENDS_ON_CACHE[$selfEntityIdent][$parentEntityUnique] = Handlers::getInstance()->getFoldedEntityByUnique($parentEntityUnique);
            }
        }
        return $this->WE_DEPENDS_ON_CACHE[$selfEntityIdent];
    }

    /**
     * Возвращает список сущностей, зависимых от сущности данного фолдинга.
     * Метод используется для оповещения сущностей, использующих данную, об изменении.
     * 
     * unique => FoldedEntity
     */
    public final function getEntitysDependableFromUs($selfEntityIdent) {
        $dependable = array();

        $selfEntityUnique = $this->getUnique($selfEntityIdent);

        /* @var $parentFolding FoldedResources */
        foreach (Handlers::getInstance()->getFoldings() as $parentFolding) {
            if ($parentFolding === $this || !$parentFolding->isCanDependsOnEntitys()) {
                continue;
            }
            foreach ($parentFolding->getVisibleIdents() as $parentEntityIdent) {
                if (array_key_exists($selfEntityUnique, $parentFolding->getEntitysWeDependsOn($parentEntityIdent))) {
                    $entity = $parentFolding->getFoldedEntity($parentEntityIdent);
                    $dependable[$entity->getUnique()] = $entity;
                }
            }
        }

        return $dependable;
    }

    /*
     * == ПАРАМЕТРЫ ЦЕПОЧЕК ЗАВИСИМОСТЕЙ ==
     */

    /**
     * Метод проверяет, были ли к данному моменту обнаружены изменённые сущности фолдинга.
     */
    public function isChangedEntitysDetected() {
        return count($this->CHANGED_ENTITYS) > 0;
    }

    /**
     * Метод проверяет, есть ли изменённые сущности данного фолдинга при этом будет выполнена и сама проверка.
     * 
     * @param boolean $all - признак, проверить ли все сущности, или достаточно определить первую изменённую.
     */
    private $AllEntityChangedChecked = false;

    private function checkEntitiesForChange($all = true) {
        if ($this->AllEntityChangedChecked) {
            return $this->isChangedEntitysDetected(); //---
        }

        $this->LOGGER->info('Checking {} for change.', $all ? 'all entitys' : 'first entity');

        if (!$all && $this->isChangedEntitysDetected()) {
            return true;
        }

        /*
         * Пробегаем по нашим сущностям
         */
        foreach ($this->getVisibleIdents() as $ident) {
            $changed = $this->checkEntityChanged($ident);
            if ($changed && !$all) {
                return true;
            }
        }

        return $this->isChangedEntitysDetected(); //---
    }

    public function checkAllEntitiesChanged() {
        return $this->checkEntitiesForChange(true);
    }

    public function checkFirstEntityChanged() {
        return $this->checkEntitiesForChange(false);
    }

    /**
     * Проверим, не был ли какой-нибудь из файлов ресурсов изменён.
     * Если был, то нужно выполнить действия после изменения сущности.
     */
    private $CHANGE_CHECKED = array();

    private function checkEntityChanged($ident) {
        if (!$this->isVisibleEntity($ident)) {
            return false; //Не проверяем изменение для сущностей, которые пока не видны
        }

        //Проверим изменения сущностей по БД
        DbChangeListener::check();

        if (in_array($ident, $this->CHANGE_CHECKED)) {
            return in_array($ident, $this->CHANGED_ENTITYS); //---
        }

        check_condition($ident != self::PATTERN_NAME, "Некоректно проверять шаблон для сущности {$this->getEntityName()} на изменение.");

        $this->CHANGE_CHECKED[] = $ident;

        $this->LOGGER->info('');
        $this->LOGGER->info("Check is entity [$ident] changed.");
        FoldedResourcesManager::onEntityAction(FoldedResourcesManager::ACTION_ENTITY_CHECK_CHANGED, $this, $ident);

        $this->AllEntityChangedChecked = count($this->CHANGE_CHECKED) >= count($this->getVisibleIdents());
        if ($this->AllEntityChangedChecked) {
            $this->LOGGER->info('All entities was checked for change.');
            FoldedResourcesManager::onEntityAction(FoldedResourcesManager::ACTION_FOLDING_ALL_CHECKED, $this);
        }


        $changed = !$this->getFromFoldedCache($ident, self::CACHE_CHANGE_PROCESS);

        /**
         * Если сущность не изменена, но есть фолдинги, от которых мы зависим - пробежимся по ним, 
         * получим сущности родительских фолдингов, от которых мы зависим и проверим, не изменились ли они.
         */
        if (!$changed && $this->isCanDependsOnEntitys($ident)) {
            $this->LOGGER->info("Entity [$ident] not need process change, but checking entitys we depends on.");

            /* @var $entity FoldedEntity */
            foreach ($this->getEntitysWeDependsOn($ident) as $entity) {
                $pchanged = $entity->getFolding()->checkEntityChanged($entity->getIdent());
                /*
                 * Даже если мы нашли изменённую дочернюю сущность, всё равно проверим все дочерние сущности.
                 */
                $changed = $changed || $pchanged;
                $this->LOGGER->info("Parent entity [$entity] is {}.", $pchanged ? 'CHANGED' : 'not chenged');
            }
        }

        $this->LOGGER->info('Entity [{}] {} process change.', $ident, $changed ? 'NEED' : 'not need');


        if ($changed) {
            //Мы изменились, прийдётся обрабатывать изменение
            $this->onEntityChanged($ident);
        }

        return $changed;
    }

    private $CHANGED_ENTITYS = array();

    //Метод вызывается, как только обнаруживается, что сущность изменилась
    public function onEntityChanged($ident) {
        if (!$this->isVisibleEntity($ident)) {
            return; //Сущность пока не видна пользователям, для неё не обрабатываем событие изменения
        }

        if (in_array($ident, $this->CHANGED_ENTITYS)) {
            return; //---
        }
        $this->CHANGED_ENTITYS[] = $ident;

        //Эту сущность больше не нужно проверять
        $this->CHANGE_CHECKED[] = $ident;
        $this->CHANGE_CHECKED = array_unique($this->CHANGE_CHECKED);

        $this->LOGGER->info("Entity [$ident] is changed");
        FoldedResourcesManager::onEntityAction(FoldedResourcesManager::ACTION_ENTITY_CHANGED, $this, $ident);

        $this->cleanFoldedCache($ident);
        $this->getAutogenDm($ident)->clearDir();
        $this->rebuildSprite($ident);

        unset($this->OLDEST[$ident]);
        unset($this->FETCH_RETURNS[$ident]);
        unset($this->WE_DEPENDS_ON_CACHE[$ident]);

        $this->onEntityChangedImpl($ident);

        $this->onFoldingChanged();

        //Транслируем событие изменения сущности во все зависимые (родительские) фолдинги
        /* @var $entity FoldedEntity */
        foreach ($this->getEntitysDependableFromUs($ident) as $parentEntity) {
            $this->LOGGER->info("Notify dependable folded entity [$parentEntity] that entity [$ident] is changed.");
            $parentEntity->onEntityChanged();
        }

        //Именно здесь ставим маркер обработанного изменения, так как до этого мы почистили кэши
        $this->saveToFoldedCache(true, $ident, self::CACHE_CHANGE_PROCESS);
    }

    /**
     * Метод вызывается в том случае, когда фолдинг меняется, а именно:
     * 1. Меняется любая сущность этого фолдинга
     * 2. Меняется таблица или представление, с которой работает этот фолдинг
     * 
     * Метод нужен восновном для сброса кешей, зависящих от этого волдинга
     */
    private $foldingChangeNotified = false;

    public final function onFoldingChanged() {
        if ($this->foldingChangeNotified) {
            return; //---
        }
        $this->foldingChangeNotified = true;
        $this->LOGGER->info('Вызван метод onFoldingChanged.');
        FoldedResourcesManager::onEntityAction(FoldedResourcesManager::ACTION_FOLDING_ONCE_CHENGED, $this);
        PSCache::inst()->onFoldingChanged($this);
    }

    protected abstract function onEntityChangedImpl($ident);

    /**
     * ====================
     * = ПРОВЕРКА ДОСТУПА =
     * ====================
     * 
     * Сущности фолдинга могут быть проверены на доступ по трём параметрам:
     * 1. Сущность существует - просто проверяется наличие директории для сущности фолдинга
     * 2. Сущность видима - для классов, хранящих свои сущности ещё и в базе. "Видима" === "Доступна клиенту".
     * 3. Сущность доступна - у пользователя есть доступ к данной сущности. Админ = сущность существует, клиет = сущность видима.
     * 
     * Видимость (в отличае от доступности) не зависит от того, под кем авторизован пользователь!
     */
    private $ALL_IDENTS;
    private $VISIBLE_IDENTS;
    private $ACCESS_IDENTS;
    private $IDENTS_LOADED = false;

    /** @return FoldedResources */
    private function IDENTS() {
        if ($this->IDENTS_LOADED) {
            return $this;
        }
        $this->IDENTS_LOADED = true;

        $this->profilerStart(__FUNCTION__);

        /*
         * 1. Загружаем список существующих сущностей фолдинга
         */
        $full = $this->getResourcesDm()->getSubDirNames();
        $this->ALL_IDENTS['full'] = $full; //С шаблоном

        $short = $full;
        array_remove_value($short, self::PATTERN_NAME);
        $short = array_values($short);
        $this->ALL_IDENTS['short'] = $short; //Без шаблона (+ нужно реиндексировать массив)

        /*
         * 2. Загружаем список видимых сущностей фолдинга
         */
        if ($this->isWorkWithTable()) {
            $this->VISIBLE_IDENTS = array_values(array_intersect($short, FoldingBean::inst()->getIdents($this, false)));
        } else {
            $this->VISIBLE_IDENTS = $short;
        }

        /*
         * 3. Строим список сущностей фолдинга, к которым пользователь имеет доступ
         */
        if (AuthManager::isAuthorizedAsAdmin()) {
            //Админ имеет доступ и к шаблону
            $this->ACCESS_IDENTS['full'] = $full;
            $this->ACCESS_IDENTS['short'] = $short;
        } else {
            $this->ACCESS_IDENTS['full'] = $this->VISIBLE_IDENTS;
            $this->ACCESS_IDENTS['short'] = $this->VISIBLE_IDENTS;
        }

        /*
         * 5. Отлогируем то, что получилось
         */
        if ($this->LOGGER->isEnabled()) {
            $this->LOGGER->info();
            $this->LOGGER->info('Загружаем список доступных сущностей');
            $this->LOGGER->info('Все сущности ({}): {}', count($full), array_to_string($full));

            $hidden = array_diff($full, $this->VISIBLE_IDENTS);
            if (empty($hidden)) {
                $this->LOGGER->info('Скрытых сущностей нет');
            } else {
                $this->LOGGER->info('Cкрытые сущности ({}): {}', count($hidden), array_to_string($hidden));
            }

            $this->LOGGER->info('Доступные пользователю сущности ({}): {}', count($this->ACCESS_IDENTS['full']), array_to_string($this->ACCESS_IDENTS['full']));

            $this->LOGGER->info();

            $this->profilerStop();
        }

        return $this;
    }

    /**
     * Возвращает полный список сущностей, не проверяя права доступа
     */
    public final function getAllIdents($includePattern = false) {
        return $this->IDENTS()->ALL_IDENTS[$includePattern ? 'full' : 'short'];
    }

    /**
     * Список сущностей, видимых пользователям
     */
    public final function getVisibleIdents() {
        return $this->IDENTS()->VISIBLE_IDENTS;
    }

    public final function getAccessibleIdents($includePattern = false) {
        return $this->IDENTS()->ACCESS_IDENTS[$includePattern ? 'full' : 'short'];
    }

    /**
     * Метод проверяет существование директории для сущности фолдинга.
     */
    public function existsEntity($ident) {
        return in_array($ident, $this->getAllIdents(true));
    }

    /**
     * Метод проверяет видимость сущности фолдинга.
     * Восновном используется для сущностей, хранимых ещё и в базе. 
     * Если записи в базе нет или она имеет признак b_show=0, то сущность не будет видна.
     * 
     * Кеш строится только в зависимости от видимых сущностей фолдинга, на изменение тех сущностей,
     * которые не видны пользователю, не будет реакции. Это нужно, например, для того, чтобы админ
     * мог редактировать фолдинг в админке, при этом не происходил сброс кешей.
     */
    private function isVisibleEntity($ident) {
        return in_array($ident, $this->getVisibleIdents());
    }

    /**
     * Метод проверяет, имеет ли текущий авторизованный пользователь доступ к сущности фолдинга.
     * Админ может иметь доступ к существующим невидимым сущностям, например если он её только создал, но не доабвил запись в базу.
     */
    public function hasAccess($ident, $checkClassInstAccess = false) {
        if (!in_array($ident, $this->getAccessibleIdents(true))) {
            return false;
        }
        if ($checkClassInstAccess) {
            return $this->isAllowedResourceType(self::RTYPE_PHP) && $this->getEntityClassInst($ident)->isUserHasAccess();
        }
        return true;
    }

    /**
     * Экземпляры всех классов для видимых сущностей фолдинга
     */
    public final function getVisibleClassInsts() {
        $insts = array();
        foreach ($this->getVisibleIdents() as $ident) {
            $insts[$ident] = $this->getEntityClassInst($ident);
        }
        return $insts;
    }

    /**
     * Названия всех классов для всех сущностей фолдинга, доступных пользователю.
     * Восновном используется для вызова статичиских методов.
     */
    public final function getAccessibleClassNames() {
        $this->assertAllowedResourceType(self::RTYPE_PHP);
        $classNames = array();
        foreach ($this->getAccessibleIdents() as $ident) {
            require_once $this->getClassPath($ident);
            $classNames[$ident] = $this->ident2className($ident);
        }
        return $classNames;
    }

    /**
     * Метод вернёт экземпляры классов для всех сущностей, доступных пользователю
     */
    public final function getAllUserAcessibleClassInsts() {
        return $this->getUserAcessibleClassInsts($this->getAccessibleIdents());
    }

    /**
     * Метод принимает на вход массив идентификаторов и возвращает те из них, 
     * которые видны пользователю и экземпляры классов для которых имеют тип доступа,
     * видимый текущему авторизованному пользователю.
     */
    protected final function getUserAcessibleClassInsts(array $idents) {
        $insts = array();
        foreach ($idents as $ident) {
            if (!array_key_exists($ident, $insts) && $this->hasAccess($ident, true)) {
                $insts[$ident] = $this->getEntityClassInst($ident);
            }
        }
        return $insts;
    }

    //Сущность существует, но не обязательно видима
    public function assertExistsEntity($ident) {
        check_condition($this->existsEntity($ident), "Элемент {$this->getTextDescr($ident)} не существует.");
    }

    //Проверяет, что сущность не существует
    public function assertNotExistsEntity($ident) {
        check_condition(!$this->existsEntity($ident), "Элемент {$this->getTextDescr($ident)} уже существует.");
    }

    private function assertHasAccess($ident, $doAssert = true) {
        if ($doAssert) {
            $this->assertExistsEntity($ident);
            check_condition($this->hasAccess($ident), 'Вы не имеете доступа к сущности ' . $this->getTextDescr($ident));
        }
    }

    /**
     * Метод возвращает сущность фолдинга
     * 
     * @return FoldedEntity
     */
    public function getFoldedEntity($ident, $ensureHasAccess = false) {
        $this->assertHasAccess($ident, $ensureHasAccess);
        return $this->hasAccess($ident) ? FoldedEntity::inst($this, $ident) : null;
    }

    /**
     * Метод возвращает все сущности фолдинга
     * @return array
     */
    public function getAccessibleFoldedEntitys($includePattern = false) {
        $result = array();
        foreach ($this->getAccessibleIdents($includePattern) as $ident) {
            $result[] = $this->getFoldedEntity($ident);
        }
        return $result;
    }

    public function getVisibleFoldedEntitys() {
        $result = array();
        foreach ($this->getVisibleIdents() as $ident) {
            $result[] = $this->getFoldedEntity($ident);
        }
        return $result;
    }

    public function getAllowedResourceTypes() {
        return $this->RESOURCE_TYPES_ALLOWED;
    }

    public function isAllowedResourceType($type) {
        return in_array($type, $this->RESOURCE_TYPES_ALLOWED);
    }

    public function assertAllowedResourceType($type) {
        check_condition($this->isAllowedResourceType($type), "Тип ресурса [$type] не может быть запрошен для сущностей типа " . $this->getTextDescr());
    }

    /**
     * Создание экземпляра класса для сущности фолдинга
     * @return FoldedClass
     */
    public function getEntityClassInst($ident, $cache = true) {
        if (!$cache || !$this->INSTS_CACHE->has($ident)) {
            //Получим элемент - класс
            $php = $this->getResourceDi($ident, self::RTYPE_PHP);

            //Проверим, что это - файл
            check_condition($php->isFile(), 'Не найден класс реализации для сущности ' . $this->getTextDescr($ident));

            //Проверим сущность на изменение
            $this->checkEntityChanged($ident);

            //Получим FoldedEntity, так как её потом нужно будет передать в конструктор
            $foldedEntity = $this->getFoldedEntity($ident);

            //Подключим класс, не будем заставлять трудиться класслоадер
            require_once $php->getAbsPath();

            //Построим название класса на основе идентификатора сущности
            $baseFoldedClass = 'FoldedClass';
            $class = $this->ident2className($ident);
            check_condition(PsUtil::isInstanceOf($class, $baseFoldedClass), "Класс для сущности $foldedEntity не является наследником $baseFoldedClass");

            //Создаём акземпляр
            $inst = new $class($foldedEntity);

            //Отлогируем
            $this->LOGGER->info("Instance of $class created.");
            FoldedResourcesManager::onEntityAction(FoldedResourcesManager::ACTION_ENTITY_INST_CREATED, $this, $ident);

            return $cache ? $this->INSTS_CACHE->set($ident, $inst) : $inst;
        }
        return $this->INSTS_CACHE->get($ident);
    }

    /** @return DirManager */
    public function getResourcesDm($ident = null, $subDir = null) {
        $this->assertHasAccess($ident, !!$ident);
        return DirManager::resources(array('folded', $this->getFoldingGroup(), $ident, $subDir));
    }

    public static function resourceTypeToExt($type) {
        return array_get_value($type, self::$TYPE2EXT, $type);
    }

    /** @return DirItem */
    public function getResourceDi($ident, $type) {
        $this->assertHasAccess($ident);
        $this->assertAllowedResourceType($type);
        return $this->getResourcesDm()->getDirItem($ident, $ident, self::resourceTypeToExt($type));
    }

    /** @return DirItem */
    public function getTplDi($ident) {
        return $this->getResourceDi($ident, self::RTYPE_TPL);
    }

    /**
     * Метод возвращает менеджера информационной директории
     * @return DirManager
     */
    private function getInfoDm($ident) {
        $this->assertHasAccess($ident);
        return $this->getResourcesDm($ident, self::INFO_PATTERNS);
    }

    /**
     * Метод возвращает шаблон из информационной директории
     * @return DirItem
     */
    private function getInfoDi($ident, $tplPath) {
        return $this->getInfoDm($ident)->getDirItem(null, $tplPath, 'tpl');
    }

    /**
     * Метод возвращает список всех информационных шаблонов
     */
    public function getInfoDiList($ident) {
        return $this->getInfoDm($ident)->getDirContentFull(null, PsConst::EXT_TPL);
    }

    /** @return Smarty_Internal_Template */
    private function getTpl($ident, $smartyParams = null) {
        return PSSmarty::template($this->getResourceDi($ident, self::RTYPE_TPL), $smartyParams);
    }

    public function getAccesibleResourcesDi($type, $includePattern = false) {
        $result = array();
        foreach ($this->getAccessibleIdents($includePattern) as $ident) {
            $result[$ident] = $this->getResourceDi($ident, $type);
        }
        return $result;
    }

    /*
     * ИНФОРМАЦИЯ О ФОЛДИНГЕ, ХРАНИМАЯ В ШАБЛОНАХ
     * 
     * Информационные шаблоны хранятся в папке tpl, рядом с ресурсами фолдинга.
     */

    /** @return FoldedInfoTpl */
    public function getInfoTpl($ident, $tplPath) {
        return FoldedInfoTpl::inst($this->getFoldedEntity($ident, true), $this->getInfoDi($ident, $tplPath));
    }

    private $INFO_TEMPLATES_LISTS = array();

    public function getInfoTpls($ident, $tplDir = null) {
        $key = unique_from_path($ident, $tplDir);
        if (!array_key_exists($key, $this->INFO_TEMPLATES_LISTS)) {
            $this->INFO_TEMPLATES_LISTS[$key] = array();
            $entity = $this->getFoldedEntity($ident, true);
            foreach ($this->getInfoDm($ident)->getDirContent($tplDir, PsConst::EXT_TPL) as $tplDi) {
                $this->INFO_TEMPLATES_LISTS[$key][] = FoldedInfoTpl::inst($entity, $tplDi);
            }
        }
        return $this->INFO_TEMPLATES_LISTS[$key];
    }

    private $ALL_INFO_TEMPLATES_LISTS;

    public function getAllInfoTpls($ident) {
        if (!is_array($this->ALL_INFO_TEMPLATES_LISTS)) {
            $this->ALL_INFO_TEMPLATES_LISTS = array();
            $entity = $this->getFoldedEntity($ident, true);
            /* @var $tplDi DirItem */
            foreach ($this->getInfoDm($ident)->getDirContentFull(null, PsConst::EXT_TPL) as $tplDi) {
                $this->ALL_INFO_TEMPLATES_LISTS[] = FoldedInfoTpl::inst($entity, $tplDi);
            }
        }
        return $this->ALL_INFO_TEMPLATES_LISTS;
    }

    /**
     * Метод возвращат путь относительно директории информационных шаблонов данного фолдинга:
     * /resources/folded/stocks/mosaic/tpl/stock1.tpl -> /stock1.tpl
     * 
     * Пример вызова:
     * StockManager::inst()->getInfoTplRelPath('/resources/folded/stocks/mosaic/tpl/stock1.tpl');
     */
    public function getInfoTplRelPath($infoTpl) {
        $infoTpl = $infoTpl instanceof DirItem ? $infoTpl->getRelPath() : $infoTpl;
        $infoTpl = $infoTpl instanceof FoldedInfoTpl ? $infoTpl->getDirItem()->getRelPath() : $infoTpl;

        $rel2foldDm = cut_string_start($infoTpl, $this->getResourcesDm()->relDirPath());

        check_condition($rel2foldDm != $infoTpl, "Путь [$infoTpl] не принадлежит фолдингу $this.");

        $ident = array_get_value(0, explode(DIR_SEPARATOR, $rel2foldDm));

        check_condition($this->existsEntity($ident), "Не удалось определить сущность фолдинга для информационного шаблона [$infoTpl].");

        $rel2infoDm = cut_string_start($infoTpl, $this->getInfoDm($ident)->relDirPath());

        check_condition($rel2infoDm != $infoTpl, "Путь [$infoTpl] не является путём к информационному шаблону.");

        return ensure_dir_startswith_dir_separator($rel2infoDm);
    }

    /**
     * Метод возвращает информацию из информационного шаблона, производя его фетчинг
     * с переданными параметрами Smarty.
     */
    public function getInfo($ident, $tpl, array $smartyParams = array()) {
        //Информационный шаблон
        $tpl = $tpl instanceof DirItem ? $tpl : $this->getInfoDi($ident, $tpl);
        //Если шаблон не сущетвует - просто пропускаем его, и не будем ругаться
        if (!$tpl->isFile()) {
            return null; //---
        }
        //Ключом являются параметры Смарти, с которыми мы фетчим информационный шаблон
        $cacheKey = simple_hash($smartyParams);
        //Идентификатором кеша является путь к файлу шаблона
        $cacheId = self::CACHE_INFO_TPL . md5($tpl->getRelPath());
        //Пробуем загрузить закешированный 
        $cached = to_array($this->getFromFoldedCache($ident, $cacheId, array()));

        if (!array_key_exists($cacheKey, $cached)) {
            $cached[$cacheKey] = $this->getInfoTplCtt($ident, $tpl, $smartyParams);
            $this->saveToFoldedCache($cached, $ident, $cacheId);
        }

        return $cached[$cacheKey];
    }

    /**
     * Фетчинг информационного шаблона без его кеширования
     */
    public function getInfoTplCtt($ident, $tpl, array $smartyParams = array()) {
        $this->assertHasAccess($ident);
        $tpl = $tpl instanceof DirItem ? $tpl : $this->getInfoDi($ident, $tpl);
        FoldedInfoTplContext::getInstance()->setContextWithFoldedEntity($this->getFoldedEntity($ident, true));
        $content = trim(ContentHelper::getContent(PSSmarty::template($tpl, $smartyParams)));
        FoldedInfoTplContext::getInstance()->dropContext();
        return $content;
    }

    /**
     * ИНФОРМАЦИЯ О ФОЛДИНГЕ, ХРАНИМАЯ В ТЕКСТОВОМ ФАЙЛЕ:
     * [param1]
     *  value1
     */
    public function getTxtParam($ident, $param, $default = null) {
        $this->assertHasAccess($ident);
        $this->assertAllowedResourceType(self::RTYPE_TXT);
        $params = $this->getFromFoldedCache($ident, self::CACHE_TXT_PARAMS, array());
        if (!is_array($params)) {
            $params = $this->getResourceDi($ident, self::RTYPE_TXT)->getTextPropsAdapter()->getProps();
            $this->saveToFoldedCache($params, $ident, self::CACHE_TXT_PARAMS);
        }
        return array_get_value($param, $params, $default);
    }

    /**
     * Различные "временные" данные для сущности
     */

    /** @return DirManager */
    public function getAutogenDm($ident, $subDir = null) {
        $this->assertHasAccess($ident);
        $this->checkEntityChanged($ident);
        return DirManager::autogen(array('folded', $this->getFoldingGroup(), $ident, $subDir));
    }

    /** @return DirItem */
    public function getAutogenDi($ident, $subDir = null, $file = null, $ext = null) {
        return $this->getAutogenDm($ident, $subDir)->getDirItem(null, $file, $ext);
    }

    /**
     * Метод подключает ресурсы к сущности фолдинга.
     * Всегда подключаем все ресурсы, ненужные будут выкинуты в процессе финализации страницы.
     */
    public function getResourcesLinks($ident, $content = null) {
        $this->assertHasAccess($ident);

        $this->LOGGER->info("Getting resource links for entity [$ident].");

        $tokens = array();
        foreach ($this->RESOURCE_TYPES_LINKED as $type) {
            $di = $this->getResourceDi($ident, $type);
            switch ($type) {
                case self::RTYPE_JS:
                    $tokens[] = PsHtml::linkJs($di);
                    break;
                case self::RTYPE_CSS:
                    $tokens[] = PsHtml::linkCss($di);
                    break;
                case self::RTYPE_PCSS:
                    $tokens[] = PsHtml::linkCss($di, 'print');
                    break;
            }
        }
        //Приаттачим спрайты
        $sprite = $this->getSprite($ident);
        $tokens[] = $sprite ? PsHtml::linkCss($sprite->getCssDi()) : '';

        //Контент - после ресурсов
        $tokens[] = $content;
        return concat($tokens);
    }

    /**
     * Фетчинг шаблона и добавление к нему ресурсов
     */
    public function fetchTplWithResources($ident, $smParams = null, $returnType = self::FETCH_RETURN_CONTENT) {
        return $this->fetchTplImpl($ident, $smParams, $returnType, true);
    }

    const FETCH_RETURN_FULL = 'full';
    const FETCH_RETURN_CONTENT = 'content';
    const FETCH_RETURN_PARAMS = 'params';
    const FETCH_RETURN_FULL_OB = 'full_ob';
    const FETCH_RETURN_PARAMS_OB = 'params_ob';

    private $FETCH_RETURNS = array();
    private static $FETCH_REQUEST_CNT = 0;

    public function fetchTplImpl($ident, $smParams = null, $returnType = self::FETCH_RETURN_CONTENT, $addResources = false, $cacheId = null) {
        $this->assertHasAccess($ident);

        $logMsg = null;

        if ($this->LOGGER->isEnabled()) {
            $rqNum = ++self::$FETCH_REQUEST_CNT;
            $logMsg = "#$rqNum Smarty params count: " . count(to_array($smParams)) . ", type: $returnType, resources: " . var_export($addResources, true) . ", " . ($cacheId ? "cache id: [$cacheId]" : 'nocache');
            $this->LOGGER->info("Tpl fetching requested for entity [$ident]. $logMsg");
            FoldedResourcesManager::onEntityAction(FoldedResourcesManager::ACTION_ENTITY_FETCH_REQUESTD, $this, $ident, $logMsg);
        }

        $entity = $this->getFoldedEntity($ident);

        //Сразу установим зависимость от текущей сущности
        FoldedContextWatcher::getInstance()->setDependsOnEntity($entity);

        $CTXT = $this->getFoldedContext();

        $PCLASS = $CTXT->tplFetchParamsClass();
        $PCLASS_BASE = FoldedTplFetchPrams::getClassName();

        check_condition(PsUtil::isInstanceOf($PCLASS, $PCLASS_BASE), "Класс [$PCLASS] для хранения данных контекста $CTXT должен быть подклассом $PCLASS_BASE");

        //Если мы не возвращаем содержимое, то в любом случае ресурсы добавлять не к чему
        $addResources = $addResources && !in_array($returnType, array(self::FETCH_RETURN_PARAMS, self::FETCH_RETURN_PARAMS_OB));

        $keysRequired = PsUtil::getClassConsts($PCLASS, 'PARAM_');
        $keysRequiredParams = array_diff($keysRequired, array(FoldedTplFetchPrams::PARAM_CONTENT));

        $PARAMS = null;
        $PARAMS_KEY = null;

        $CONTENT = null;
        $CONTENT_KEY = null;

        $RETURN_KEY = null;

        if ($cacheId) {
            $cacheId = ensure_wrapped_with($cacheId, '[', ']') . '[' . PsDefines::getReplaceFormulesType() . ']';
            $RETURN_KEY = $cacheId . '-' . $returnType;

            if (array_key_exists($ident, $this->FETCH_RETURNS)) {
                if (array_key_exists($RETURN_KEY, $this->FETCH_RETURNS[$ident])) {
                    return $this->FETCH_RETURNS[$ident][$RETURN_KEY];
                }
            } else {
                $this->FETCH_RETURNS[$ident] = array();
            }

            $PARAMS_KEY = empty($keysRequiredParams) ? null : $cacheId . '-params';
            $CONTENT_KEY = $cacheId . '-content';

            switch ($returnType) {
                case self::FETCH_RETURN_FULL:
                case self::FETCH_RETURN_FULL_OB:
                    $CONTENT = $this->getFromFoldedCache($ident, $CONTENT_KEY);
                    $PARAMS = $PARAMS_KEY ? $this->getFromFoldedCache($ident, $PARAMS_KEY, $keysRequiredParams) : array();
                    if ($CONTENT && is_array($PARAMS)) {
                        $CONTENT = $addResources ? $this->getResourcesLinks($ident, $CONTENT) : $CONTENT;
                        $PARAMS[FoldedTplFetchPrams::PARAM_CONTENT] = $CONTENT;
                        switch ($returnType) {
                            case self::FETCH_RETURN_FULL:
                                return $this->FETCH_RETURNS[$ident][$RETURN_KEY] = $PARAMS;
                            case self::FETCH_RETURN_FULL_OB:
                                return $this->FETCH_RETURNS[$ident][$RETURN_KEY] = new $PCLASS($PARAMS);
                            default:
                                raise_error("Unprocessed fetch return type [$returnType].");
                        }
                    }
                    break;

                case self::FETCH_RETURN_CONTENT:
                    $CONTENT = $this->getFromFoldedCache($ident, $CONTENT_KEY);
                    if ($CONTENT) {
                        $CONTENT = $addResources ? $this->getResourcesLinks($ident, $CONTENT) : $CONTENT;
                        return $this->FETCH_RETURNS[$ident][$RETURN_KEY] = $CONTENT;
                    }
                    break;

                case self::FETCH_RETURN_PARAMS:
                case self::FETCH_RETURN_PARAMS_OB:
                    $PARAMS = $PARAMS_KEY ? $this->getFromFoldedCache($ident, $PARAMS_KEY, $keysRequiredParams) : array();
                    if (is_array($PARAMS)) {
                        switch ($returnType) {
                            case self::FETCH_RETURN_PARAMS:
                                return $this->FETCH_RETURNS[$ident][$RETURN_KEY] = $PARAMS;
                            case self::FETCH_RETURN_PARAMS_OB:
                                return $this->FETCH_RETURNS[$ident][$RETURN_KEY] = new $PCLASS($PARAMS);
                            default:
                                raise_error("Unprocessed fetch return type [$returnType].");
                        }
                    }
                    break;
            }
        }

        $settedNow = false;
        if (!$entity->equalTo(FoldedContextWatcher::getInstance()->getFoldedEntity())) {
            $CTXT->setContextWithFoldedEntity($entity);
            $settedNow = true;
        }

        try {
            $CONTENT = $this->getTpl($ident, $smParams)->fetch();

            $entityNow = FoldedContextWatcher::getInstance()->getFoldedEntity();
            check_condition($entity->equalTo($entityNow), "After tpl fetching folded entity [$entity] chenged to [$entityNow]");

            $PARAMS_FULL = $CTXT->finalizeTplContent($CONTENT);

            check_condition(is_array($PARAMS_FULL), "After [$entity] tpl finalisation not array is returned");
            $keysReturned = array_keys($PARAMS_FULL);

            if (count(array_diff($keysReturned, $keysRequired)) || count(array_diff($keysRequired, $keysReturned))) {
                raise_error("After [$entity] tpl finalisation required keys: " . array_to_string($keysRequired) . '], returned keys: [' . array_to_string($keysReturned) . ']');
            }

            if ($this->LOGGER->isEnabled()) {
                $this->LOGGER->info("Tpl fetching actually done for entity [$ident]. $logMsg");
                FoldedResourcesManager::onEntityAction(FoldedResourcesManager::ACTION_ENTITY_FETCH_DONE, $this, $ident, $logMsg);
            }
        } catch (Exception $e) {
            /*
             * Произошла ошибка!
             * 
             * Если мы устанавливали контенст и он не поменялся после завершения фетчинга (если поменялся, это ошибка), то нужно его обязательно завершить.
             * Если контекст был установлен во внешнем блоке, то этот блок должен позаботиться о сбросе контекста.
             * 
             * Далее от нас требуется только пробросить ошибку наверх.
             */
            if ($settedNow && ($entity->equalTo(FoldedContextWatcher::getInstance()->getFoldedEntity()))) {
                $CTXT->dropContext();
            }

            throw $e;
        }
        $CONTENT = $PARAMS_FULL[FoldedTplFetchPrams::PARAM_CONTENT];

        $PARAMS = $PARAMS_FULL;
        unset($PARAMS[FoldedTplFetchPrams::PARAM_CONTENT]);

        if ($PARAMS_KEY) {
            $this->saveToFoldedCache($PARAMS, $ident, $PARAMS_KEY);
        }

        if ($CONTENT_KEY) {
            $this->saveToFoldedCache($CONTENT, $ident, $CONTENT_KEY);
        }

        if ($settedNow) {
            $CTXT->dropContext();
        }

        if ($addResources) {
            $CONTENT = $this->getResourcesLinks($ident, $CONTENT);
            $PARAMS_FULL[FoldedTplFetchPrams::PARAM_CONTENT] = $CONTENT;
        }

        switch ($returnType) {
            case self::FETCH_RETURN_FULL:
                return $RETURN_KEY ? $this->FETCH_RETURNS[$ident][$RETURN_KEY] = $PARAMS_FULL : $PARAMS_FULL;
            case self::FETCH_RETURN_FULL_OB:
                return $RETURN_KEY ? $this->FETCH_RETURNS[$ident][$RETURN_KEY] = new $PCLASS($PARAMS_FULL) : new $PCLASS($PARAMS_FULL);
            case self::FETCH_RETURN_CONTENT:
                return $RETURN_KEY ? $this->FETCH_RETURNS[$ident][$RETURN_KEY] = $CONTENT : $CONTENT;
            case self::FETCH_RETURN_PARAMS:
                return $RETURN_KEY ? $this->FETCH_RETURNS[$ident][$RETURN_KEY] = $PARAMS : $PARAMS;
            case self::FETCH_RETURN_PARAMS_OB:
                return $RETURN_KEY ? $this->FETCH_RETURNS[$ident][$RETURN_KEY] = new $PCLASS($PARAMS) : new $PCLASS($PARAMS);
        }

        raise_error("Unknown fetch return type [$returnType].");
    }

    /** @return FoldedContext */
    protected function getFoldedContext() {
        return FoldedContext::getInstance();
    }

    /**
     * Метод возвращает путь к классу для сущности фолдинга
     */
    public function getClassPath($ident) {
        if (!$this->CLASS_PATH_BASE || $ident === self::PATTERN_NAME) {
            return null;
        }
        return $this->CLASS_PATH_BASE . $ident . DIR_SEPARATOR . $ident . '.php';
    }

    /*
     * COVERS
     */

    /**
     * Метод возвращает признак - работает ли данный фолдинг с картинками
     */
    public function isImagesFactoryEnabled() {
        return $this instanceof ImagedFolding;
    }

    /**
     * Метод утверждает, что фолдинг работает с картинками
     */
    public function assertImagesFactoryEnabled() {
        check_condition($this->isImagesFactoryEnabled(), "Фолдинг [$this] не работает с картинками");
    }

    /**
     * Метод возвращает путь к обложке для сущности
     * 
     * @return DirItem
     */
    private function getCoverOriginal($ident) {
        $this->assertHasAccess($ident);
        $this->assertImagesFactoryEnabled();
        return $this->getResourcesDm()->getDirItem($ident, $ident, SYSTEM_IMG_TYPE);
    }

    /**
     * Метод возвращает обложку сущности, приводя её размер к $dim.
     * 
     * @param type $ident - идентификатор сущности. Если передан null, то будет возвращена дефолтная обложка.
     * @param type $dim - размер в виде 3x4
     * @return DirItem
     */
    public function getCover($ident = null, $dim = null) {
        $this->assertImagesFactoryEnabled();

        //Определим размер
        $dim = $dim ? $dim : $this->defaultDim();

        //Мы ожидаем, что все обложки для фолдингов должны иметь расширение SYSTEM_IMG_TYPE
        $scrDi = $ident && $this->hasAccess($ident) ? $this->getCoverOriginal($ident) : null;

        //Передать сущность нужно именно в качестве первого параметра метода getDirItem,
        //чтобы не проверять доступ, так как дефолтная картинка должна быть доступна всегда
        $dfltDi = $this->getResourcesDm()->getDirItem(self::PATTERN_NAME, self::PATTERN_NAME, SYSTEM_IMG_TYPE);

        //Выполняем resize
        return $scrDi ? PsImgEditor::resize($scrDi, $dim, $dfltDi) : PsImgEditor::resize($dfltDi, $dim);
    }

    /*
     * SPRITES
     */

    /**
     * Признак - производится ли построение спрайтов для сущностей данного фолдинга.
     * Это происходит только в том случае, когда контекст данного волдинга наследует {@see SpritableContext}
     */
    public function isSpritable() {
        return $this->getFoldedContext() instanceof SpritableContext;
    }

    /**
     * Метод утверждает, что данный класс работает со спрайтами
     */
    protected function assertSpritable($ident) {
        check_condition($this->isSpritable(), "Работа со спрайтами для сущности {$this->getTextDescr($ident)} запрещена.");
    }

    /** @return CssSprite */
    public function getSprite($ident) {
        return $this->isSpritable() ? CssSpritesManager::getSprite($this->getFoldedEntity($ident)) : null;
    }

    private function rebuildSprite($ident) {
        if ($this->isSpritable()) {
            $this->getSprite($ident)->rebuild();
        }
    }

    /**
     * Название файла со спрайтами
     */
    public function getSpriteName($ident) {
        $this->assertSpritable($ident);
        return $this->getUnique($ident);
    }

    /**
     * Метод должен вернуть картинки для построения спрайта
     */
    public function getSpriteImages($ident) {
        $this->assertSpritable($ident);
        return $this->getTplFormules($ident);
    }

    /**
     * Метод извлекает все формулы из smarty-шаблона
     */
    public function getTplFormules($ident) {
        return TexImager::inst()->extractTexImages($this->getTplDi($ident)->getFileContents());
    }

    /*
     * ==============================
     * = LISTS - работа со списками =
     * ==============================
     */

    /**
     * Метод проверяет, может ли сущность входить в список.
     * При этом проверяется именно сама сущность, а не права доступа к ней.
     * Права будут проверены позднее.
     */
    protected abstract function isIncludeToList($ident, $list);

    /**
     * Возвращает допустимые списки, с которыми работает фолдинг.
     * Для того, чтобы объявить список - поддерживаемым, нужно в классе добавить константу
     * с префиксом LIST_
     */
    public final function getLists() {
        return PsUtil::getClassConsts(get_called_class(), 'LIST_');
    }

    /**
     * Признак - есть ли у данного фолдинга списки, с которыми можно работать
     */
    public final function hasLists() {
        return count($this->getLists()) > 0;
    }

    /**
     * Метод возвращает элемент директории для списка фолдинга
     * 
     * @param type $list
     * @return DirItem
     */
    private function getListDi($list) {
        check_condition(in_array($list, $this->getLists()), "Фолдинг $this не работает со списком [$list]");
        return $this->getResourcesDm()->getDirItem(null, $list, 'txt');
    }

    //Сущности, входящие в списки
    private $LINES = array();

    /**
     * Основной метод, возвращающий содержимое списка.
     * Пользователь получит все сущности из списка, к которым у него есть доступ.
     * 
     * @param string $list - название списка
     * @return array идентификаторы из списка
     */
    private function getListContent($list) {
        if (!array_key_exists($list, $this->LINES)) {
            //Инициализируем хранилища
            $this->LINES[$list] = array();

            //Загрузим строки
            $lines = $this->getListDi($list)->getTextFileAdapter()->getLines();
            foreach ($lines as $line) {
                $marked = ends_with($line, '+');
                $line = $marked ? cut_string_end($line, '+') : $line;
                if ($this->hasAccess($line) && $this->isIncludeToList($line, $list)) {
                    $this->LINES[$list][$line] = $marked;
                }
            }
        }
        return $this->LINES[$list];
    }

    /**
     * Метод загружает все сущности из списка
     */
    protected function getListIdents($list) {
        return array_keys($this->getListContent($list));
    }

    /**
     * Метод получает сущности из списка, создаёт экземпляры классов и проверяет 
     * их на доступность текущему авторизованному пользователю.
     */
    protected final function getUserAcessibleClassInstsFromList($list) {
        return $this->getUserAcessibleClassInsts($this->getListIdents($list));
    }

    /*
     * МЕТОДЫ ДЛЯ РАБОТЫ С БАЗОЙ
     */

    /**
     * Метод возвращает название таблицы, в которой хранятся сущности фолдинга
     */
    public function getTableName() {
        return $this->TABLE;
    }

    /**
     * Метод возвращает название вьюхи, в которой хранятся видимые сущности фолдинга
     */
    public function getTableView() {
        return $this->TABLE_VIEW;
    }

    /**
     * Метод возвращает название столбца в таблице, хранящего идентификатор фолдинга
     */
    public function getTableColumnIdent() {
        return $this->TABLE_COLUMN_IDENT;
    }

    /**
     * Метод возвращает название столбца в таблице, хранящего подтип фолдинга
     */
    public function getTableColumnStype() {
        return $this->TABLE_COLUMN_STYPE;
    }

    /**
     * Метод возвращает признак - работает ли данный фолдинг с базой
     */
    public function isWorkWithTable() {
        return $this instanceof DatabasedFolding;
    }

    /**
     * Метод утверждает, что фолдинг работает с базой
     */
    public function assertWorkWithTable() {
        check_condition($this->isWorkWithTable(), "Фолдинг $this не работает с базой");
    }

    /**
     * Метод возвращает идентификаторы сущностей фолдинга из таблицы.
     * Для админа вернёт всё (из таблицы), для обычного пользователя - только видимые (из вью).
     */
    public function getAccessibleDbIdents() {
        return FoldingBean::inst()->getIdents($this, AuthManager::isAuthorizedAsAdmin());
    }

    /**
     * Метод возвращает идентификаторы сущностей фолдинга из таблицы.
     * Для админа вернёт всё (из таблицы), для обычного пользователя - только видимые (из вью).
     */
    public function getVisibleDbObjects($objectName) {
        return FoldingBean::inst()->getVisibleObjects($this, $objectName, $this->getVisibleIdents());
    }

    /**
     * Метод возвращает из базы код для сущности.
     * На некоторые сущности фолдингов можно ссылаться по коду, как, например, на шаблонные сообщения или причину выдачи очков.
     * Мы можем не обязывать классы следить за тем, чтобы они имели сквозную нумерацию, просто будем, при 
     * необходимости, генерировать этот код.
     */
    public function getEntiltyDbCode($ident) {
        return $this->getFoldedEntity($ident, true)->getDbCode();
    }

    /**
     * Метод получает сущность фолдинга по её коду и убеждается, что она принадлежит данному фолдингу
     * 
     * @return FoldedEntity
     */
    protected function getFoldedEntityByDbCode($code) {
        $entity = FoldedResourcesManager::inst()->getFoldedEntityByDbCode($code);
        check_condition($entity->getFolding() === $this, "Сущность $entity с кодом [$code] не принадлежит фолдингу $this");
        return $entity;
    }

    /**
     * Метод возвращает "сырую" запись БД для сущности фолдинга, которая может быть использована для:
     * 1. Понимания, есть ли запись в БД для данного фолдинга
     * 2. Наполнения формы создания записи в БД (для данной сущности фолдинга)
     * 
     * Иными словами, метод возвращает то, какой ДОЛЖНА БЫТЬ запись в базе для данного фолдинга.
     * По этим данным её можно попытаться извлечь или наполнить форму создания.
     */
    public function getDbRec4Entity($ident) {
        if (!$this->isWorkWithTable()) {
            return null;
        }
        $row = $this->dbRec4Entity($ident);
        return is_array($row) ? $row : null;
    }

    /**
     * Данный метод возвращает идентификатор фолдинга для записи из таблицы
     * 
     * @param array $rec - запись из БД
     * @param type $checkEntityExists - проверить, существует ли эта сущность фолдинга
     */
    public function getEntityIdent4DbRec(array $rec, $checkEntityExists) {
        if (!$this->isWorkWithTable()) {
            return null;
        }
        if ($this->TABLE_COLUMN_STYPE && array_key_exists($this->TABLE_COLUMN_STYPE, $rec) && ($rec[$this->TABLE_COLUMN_STYPE] !== $this->getFoldingSubType())) {
            return null;
        }
        $ident = array_get_value($this->TABLE_COLUMN_IDENT, $rec);
        return $ident && (!$checkEntityExists || $this->existsEntity($ident)) ? $ident : null;
    }

    /*
     * =====================
     * = РАБОТА С ПАНЕЛЯМИ =
     * =====================
     */

    /**
     * Метод включает панель данного фолдинга на страницу.
     * Для того, чтобы фолдинг мог добавлять панели на страницу, он должен
     * наследовать интерфейс PanelFolding.
     * 
     * При построении панели может вернуться и null, тогда панель не будет добавлена.
     * 
     * @return type
     */
    private $includedPanels = array();

    public final function includePanel($panelName) {
        if (array_key_exists($panelName, $this->includedPanels)) {
            return $this->includedPanels[$panelName];
        }

        check_condition($this instanceof PanelFolding, "Фолдинг $this не может работать с панелями");
        check_condition(in_array($panelName, PsUtil::getClassConsts($this, 'PANEL_')), "Панель [$panelName] не может быть предоставлена фолдингом $this");

        //Сразу отметим, что панель была запрошена, так как может возникнуть ошибка
        $this->includedPanels[$panelName] = '';

        /*
         * Уникальный код панели - тот самый, через который потом можно будет 
         * достучаться до параметров панели из javascript.
         */
        $panelUnique = $this->getUnique($panelName);

        //Стартуем профайлер
        $this->profilerStart(__FUNCTION__ . "($panelName)");

        /** @var PluggablePanel */
        $panel = $this->buildPanel($panelName);

        //Мог вернуться и null, тогда ничего не подключаем
        if ($panel == null) {
            //Останавливаем профайлер без сохранения
            $this->profilerStop(false);
            return '';
        }

        //Останавливаем профайлер
        $this->profilerStop();

        check_condition($panel instanceof PluggablePanel, "Возвращена некорректная панель $panelUnique. Ожидался обект типа PluggablePanel, получен: " . PsUtil::getClassName($panel));

        //Html content
        $this->includedPanels[$panelName] = trim($panel->getHtml());

        //Js params
        $jsParams = $panel->getJsParams();
        if (!isTotallyEmpty($jsParams)) {
            PageBuilderContext::getInstance()->setJsParamsGroup(PsConstJs::PAGE_JS_GROUP_PANELS, $panelUnique, $jsParams);
        }

        //Smarty resources params
        $smartyParams4Resources = $panel->getSmartyParams4Resources();
        if (is_array($smartyParams4Resources) && !empty($smartyParams4Resources)) {
            PageBuilderContext::getInstance()->setSmartyParams4Resources($smartyParams4Resources);
        }

        return $this->includedPanels[$panelName];
    }

    /*
     * ====================================
     * = МЕТОДЫ, ДОСТУПНЫЕ ТОЛЬКО АДМИНАМ =
     * ====================================
     */

    private function assertAdminCanDo($__FUNCTION__, $ident) {
        $this->LOGGER->info('{} вызвана для {}', $__FUNCTION__, $ident);
        AuthManager::checkAdminAccess();
    }

    /**
     * Метод возвращает сущность фолдинга не проверяя, существует она или нет
     * 
     * @return FoldedEntity
     */
    public function getFoldedEntityAnyway($ident) {
        $this->assertAdminCanDo(__FUNCTION__, $ident);
        return FoldedEntity::inst($this, $ident, false);
    }

    /**
     * Возвращает массив сущностей, которые могут входить в список.
     * Метод нужен для наполнения текущего состояния списка для отображения в панели администратора.
     */
    public final function getPossibleListIdents($list) {
        $this->assertAdminCanDo(__FUNCTION__, $list);

        $now = $this->getListContent($list);
        $result = array();
        foreach ($now as $ident => $marked) {
            $result[$ident] = array(
                'i' => true, //included
                'm' => $marked //marked
            );
        }
        foreach ($this->getAllIdents() as $ident) {
            if (!array_key_exists($ident, $result) && $this->isIncludeToList($ident, $list)) {
                $result[$ident] = array(
                    'i' => false, //included
                    'm' => false  //marked
                );
            }
        }
        return $result;
    }

    /**
     * Метод сохраняет содержимое списка
     */
    public final function saveList($list, array $idents) {
        $this->assertAdminCanDo(__FUNCTION__, $list);
        $content = array();
        foreach ($idents as $ident => $marked) {
            if ($this->existsEntity($ident) && $this->isIncludeToList($ident, $list)) {
                $content[] = $ident . ($marked ? '+' : '');
            }
        }
        $this->getListDi($list)->writeToFile(implode("\n", $content), true);
    }

    /**
     * Обновляет обложку для сущности
     */
    public function updateEntityCover($ident, DirItem $cover = null) {
        if (!($cover instanceof DirItem) || !$this->isImagesFactoryEnabled() || !$cover->isImg()) {
            return; //---
        }

        $this->assertHasAccess($ident);
        $this->assertAdminCanDo(__FUNCTION__, $ident);

        $this->LOGGER->info('Обновляем обложку сущности');
        PsImgEditor::copy($cover, $this->getCoverOriginal($ident));
    }

    /**
     * Редактирование сущности фолдинга
     */
    public function editEntity($ident, ArrayAdapter $params) {
        $this->assertHasAccess($ident);
        $this->assertAdminCanDo(__FUNCTION__, $ident);

        foreach ($this->RESOURCE_TYPES_ALLOWED as $type) {
            if ($params->has($type)) {
                $this->getResourceDi($ident, $type)->writeToFile($params->str($type), true);
            }
        }

        //Сущность могла стать видна из-за редактирования записи в базе
        $this->LOGGER->info('Очищаем кеш доступных сущностей');
        $this->IDENTS_LOADED = false;

        $this->onEntityChanged($ident);
    }

    /**
     * Удаление сущности фолдинга
     */
    public function deleteEntity($ident) {
        $this->assertAdminCanDo(__FUNCTION__, $ident);

        if (!$this->existsEntity($ident)) {
            /*
             * Если сущности нет - не будем ругаться
             */
            return;
        }

        $this->getResourcesDm()->clearDir($ident, true);
    }

    /**
     * Создание сущности фолдинга
     */
    public function createEntity($ident) {
        $this->assertAdminCanDo(__FUNCTION__, $ident);

        if ($this->existsEntity($ident)) {
            /*
             * Просто выходим, если сущность уже создана.
             * Это нам упростит жизнь при создании сущностей ещё и в базе (битблиотек, например).
             */
            return;
        }

        //$this->assertNotExistsEntity($ident);
        $this->getResourcesDm()->makePath($ident);
        //Зачистим кеш созданных сущноестей
        $this->IDENTS_LOADED = false;
        foreach ($this->RESOURCE_TYPES_ALLOWED as $type) {
            $src = $this->getResourceDi(self::PATTERN_NAME, $type);
            $dst = $this->getResourceDi($ident, $type)->touch();

            /*
             * Теперь возмём содержимое файлов из шаблона и заменим в них pattern на идентификатор сущности.
             * Стоит учитывать, что в шаблоне может и не быть файла с таким расширением.
             */
            $content = $src->getFileContents(false, '');
            $content = str_replace('pattern', $ident, $content);
            $content = str_replace('Pattern', ucfirst($ident), $content);
            $content = str_replace('funique', $this->getUnique(), $content);
            $content = str_replace('eunique', $this->getUnique($ident), $content);
            $content = str_replace('eident', $ident, $content);
            $content = str_replace('eclassname', $this->ident2className($ident), $content);
            $dst->writeToFile($content, true);
        }

        /*
         * Создадим директории - такие-же, как у шаблона, перенеся всё их содержимое.
         */
        $dirs = $this->getResourcesDm()->getDirContent(self::PATTERN_NAME, DirItemFilter::DIRS);
        foreach ($dirs as $dir) {
            $this->getResourcesDm(self::PATTERN_NAME)->copyDirContent2Dir($dir->getName(), $this->getResourcesDm($ident)->getDirItem(), true);
        }
    }

    /*
     * ZIP EXPORT/IMPORT
     */

    /** @return DirManager */
    private $ZIP_SECRET = '42e39f9e6a0383c2d533cf6b30a86ab3';

    private function addZipContents(ZipArchive $zip, array $items) {
        foreach ($items as $item) {
            if (is_array($item)) {
                $this->addZipContents($zip, $item);
            } else if ($item instanceof DirItem) {
                $added = true;
                if ($item->isDir()) {
                    $added = $zip->addEmptyDir($item->getRelPathNoDs());
                } else if ($item->isFile()) {
                    $added = $zip->addFile($item->getAbsPath(), $item->getRelPathNoDs());
                } else {
                    //У нас не директория и не файл, просто пропускаем
                }
                check_condition($added, "Error adding file {$item->getAbsPath()} to zip");
            }
        }
    }

    /** @return DirItem */
    public function export2zip($ident) {
        $this->assertExistsEntity($ident);
        $this->assertAdminCanDo(__FUNCTION__, $ident);

        $ftype = $this->getFoldingType();
        $fsubtype = $this->getFoldingSubType();
        $name = "$ftype-$fsubtype-$ident";

        $zipDi = $this->getAutogenDi($ident, null, $name, 'zip')->remove();

        $zip = $zipDi->startZip();

        /*
         * Экспортировать будем всё содержимое + извлечём формулы из .tpl
         */
        $ITEMS = $this->getResourcesDm($ident)->getDirContentFull();
        if ($this->isAllowedResourceType(self::RTYPE_TPL)) {
            $ITEMS[] = TexImager::inst()->extractTexImages($this->getResourceDi($ident, self::RTYPE_TPL)->getFileContents(false), false, true);
        }

        $this->addZipContents($zip, $ITEMS);

        $secret = $this->ZIP_SECRET;
        $sign = md5("$name-$secret");
        $comment = "$name;$sign";

        $zip->setArchiveComment($comment);
        $zip->close();

        return $zipDi;
    }

    /**
     * Импортирует фолдинг из zip-архива
     * 
     * @param DirItem $zip - пут к архиву
     * @param type $clear - очищать ли директорию перед загрузкой архива
     * @return FoldedEntity
     */
    public function imporFromZip(DirItem $zip, $clear = false) {
        $zip = $zip->loadZip();
        $comment = $zip->getArchiveComment();

        $comment = explode(';', $comment);
        check_condition(count($comment) === 2, 'Bad zip archive sign');

        $name = explode('-', $comment[0]);
        $sign = $comment[1];

        check_condition(count($name) === 3, 'Bad zip name');

        $ftype = $name[0];
        $fsubtype = $name[1];
        $ident = $name[2];

        $this->assertAdminCanDo(__FUNCTION__, $ident);

        /*
         * Сейчас мы загружаем zip-архивы из формы, в которой содержатся тип и подтип фолдинга, 
         * так что будем ругаться, если нам передадут не наш архив.
         * В противном случае можно будет просто проверить $this->isIt($ftype, $fsubtype)
         */
        check_condition($this->isIt($ftype, $fsubtype), "Folding [$ftype]/[$fsubtype] cannot extract this zip");

        $secret = $this->ZIP_SECRET;
        $validSign = md5("$ftype-$fsubtype-$ident-$secret");
        check_condition($sign === $validSign, 'Folding archive sign is invalid');

        //Проверим, будет ли архив развёрнут в надлежащую директорию
        $dm = $this->getResourcesDm($ident);
        if ($clear) {
            $dm->clearDir();
        }

        $exportToDirs[] = $dm->getDirItem()->getRelPathNoDs();
        $exportToDirs[] = DirManager::formules()->getDirItem()->getRelPathNoDs();

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $path = $zip->getNameIndex($i);
            $valid = contains_substring($path, $exportToDirs);
            check_condition($valid, "Cant export folded to dir: [$path]");
        }

        //Разворачиваем
        $zip->extractTo(PATH_BASE_DIR);
        $zip->close();

        //Очистка старых коверов
        $this->clearGenerated($ident);

        //Оповестим об изменении сущности
        $this->onEntityChanged($ident);

        return $this->getFoldedEntity($ident);
    }

    /**
     * КОНСТРУКТОР
     */
    protected function __construct() {
        $this->CLASS = get_called_class();
        $this->INSTS_CACHE = new SimpleDataCache();
        $this->UNIQUE = self::unique($this->getFoldingType(), $this->getFoldingSubType());
        $this->LOGGER = PsLogger::inst(__CLASS__ . '-' . $this->UNIQUE);

        $this->PROFILER = PsProfiler::inst(__CLASS__);
        $this->RESOURCE_TYPES_LINKED = array_intersect($this->RESOURCE_TYPES_ALLOWED, $this->RESOURCE_TYPES_LINKED);
        $this->RESOURCE_TYPES_CHECK_CHANGE = array_intersect($this->RESOURCE_TYPES_ALLOWED, $this->RESOURCE_TYPES_CHECK_CHANGE);

        //Получим текстовое описание
        $this->TO_STRING = $this->getTextDescr();

        /*
         * Проверим, что заданы размеры обложки по умолчанию, если мы работаем с картинками
         */
        if ($this->isImagesFactoryEnabled() && !$this->defaultDim()) {
            raise_error("Не заданы размеры обложки по умолчанию для фолдинга $this");
        }

        /*
         * Последовательность, однозначно идентифицирующая фолдинг и используемыя в различных 
         * ситуациях для связи фолдинга и его сущностей, таких как:
         * смарти функции, класс и т.д.
         * Пример: trpost, pl и т.д.
         */
        $SRC_PREFIX = trim($this->getFoldingSubType()) . $this->getFoldingType();
        $this->SMARTY_PREFIX = $SRC_PREFIX;

        //Если мы используем php-классы, то проверим, корректно ли задан префикс классов
        if ($this->isAllowedResourceType(self::RTYPE_PHP)) {
            $this->CLASS_PREFIX = strtoupper($SRC_PREFIX) . '_';
            $this->CLASS_PATH_BASE = ensure_dir_endswith_dir_separator($this->getResourcesDm()->absDirPath());
        }

        //Разберём настройки хранения фолдингов в базе
        if ($this->isWorkWithTable()) {
            $dbs = explode('.', trim($this->foldingTable()));
            $this->TABLE_VIEW = array_get_value(0, $dbs);
            $this->TABLE = cut_string_start($this->TABLE_VIEW, 'v_');
            $this->TABLE_COLUMN_IDENT = array_get_value(1, $dbs);
            $this->TABLE_COLUMN_STYPE = array_get_value(2, $dbs);

            check_condition(!!$this->TABLE && !!$this->TABLE_COLUMN_IDENT, "Некорректные настройки работы с базой для фолдинга $this");

            if ($this->TABLE_COLUMN_STYPE) {
                check_condition($this->hasSubType(), "Некорректные настройки работы с базой. Фолдинг $this не имеет подтипа.");
            }
        }
    }

    public function __toString() {
        return $this->TO_STRING;
    }

}

?>