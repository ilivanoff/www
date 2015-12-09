<?php

class PSCache extends AbstractSingleton {
    /**
     * СУЩНОСТИ, ОТ КОТОРЫХ ВООБЩЕ МОГУТ ЗАВИСИТЬ ГРУППЫ КЕШИРОВАНИЯ
     * 
     * Данные сущности позволяют работать с деревом зависимости групп кеширования 
     * от этих сущностей и отслеживать свежесть логов.
     * Сами зависимости задаются обычно при помощи механизма маппингов.
     * 
     */

    const CHILD_FOLDING = 'Фолдинги';
    const CHILD_DBENTITY = 'Сущности базы';

    /**
     * Список всех зарегистрированных групп кеша
     */
    public static function getCacheGroups() {
        return PsUtil::getClassConstLikeMethods(__CLASS__);
    }

    /**
     * Кеш для новостей - будет сброшен при изменении любой сущности, отображаемой в новостях.
     * 
     * @return PSCacheInst
     */
    public static final function NEWS() {
        return PSCacheInst::inst(__FUNCTION__);
    }

    /**
     * Кеш для навигации - будет сброшен при изменении любого поста или количества опубликованных постов.
     * 
     * @return PSCacheInst
     */
    public static final function POSTS() {
        return PSCacheInst::inst(__FUNCTION__);
    }

    /**
     * Кеш для popup-страниц. Будет сброшен при изменении кол-ва видимых плагинов,
     * которое происходит при изменении поста или кол-ва видимых постов.
     * 
     * @return PSCacheInst
     */
    public static final function POPUPS() {
        return PSCacheInst::inst(__FUNCTION__);
    }

    /**
     * Кеш для галерей картинок.
     * 
     * @return PSCacheInst
     */
    public static final function GALLERY() {
        return PSCacheInst::inst(__FUNCTION__);
    }

    /**
     * Кеш для временных шкал.
     * 
     * @return PSCacheInst
     */
    public static final function TIMELINES() {
        return PSCacheInst::inst(__FUNCTION__);
    }

    /**
     * Кеш для картинок-мозаек.
     * 
     * @return PSCacheInst
     */
    public static final function MOSAIC() {
        return PSCacheInst::inst(__FUNCTION__);
    }

    /*
     * ПОЛЯ
     */

    /** @var PsLoggerInterface */
    private $LOGGER;

    /** @var PSCacheTree */
    public $TREE;

    /** @var SimpleDataCache */
    private $CACHE;

    /** @var Cache_Lite */
    private $CACHELITE;

    private function validateGroup($group) {
        /**
         * Если зависимости от сущностей БД ещё не были проверены,
         * то запустим эту проверку и больше о валидности кешей,
         * зависящих от сущностей БД, можно не заботиться.
         * 
         * Эту проверку нужно выполнять в любом случае, так как её запуск может
         * показать изменение сущностей фолдинга или самих фолдингов, работающих
         * с изменёнными таблицами/представлениями.
         */
        DbChangeListener::check();
        $this->TREE->setTypeValidated(self::CHILD_DBENTITY, 'Выполнена проверка изменений в БД');

        /**
         * Если группа не валидируема - пропускаем.
         * Она могла стать валидируемой после проверки по базе.
         */
        if (!$this->TREE->isGroupValidatable($group)) {
            return; //---
        }

        $this->LOGGER->info(" > Валидируем группу кешей [$group]");

        /*
         * Проверим, можно ли выполнить процесс и, если можно, выполним. 
         * После выполнения о свежести логов можно будет не заботиться.
         */
        $this->LOGGER->info(" ! Пытаемся выполнить внейший процесс");
        if (ExternalProcess::inst()->executeFromClient()) {
            $this->TREE->setTypeValidated(self::CHILD_FOLDING, 'Выполнен внешний процесс от имени клиента');
            return; //---
        }

        /*
         * Если мы в продакшене - прекращаем дальнейшие проверки.
         * 
         * Вообще говоря без полной проверки всех фолдингов, от которых зависит данная группа кеша,
         * считать группу провалидированной нельзя. Но если где-либо будет обнаружен изменённый
         * фолдинг, то это приведёт к вызову метода {@link #onFoldingChanged} и кеш всёже будет сброшен.
         */
        $this->LOGGER->info(" ! Проверяем режим продакшн");
        if (PsDefines::isProduction()) {
            $this->TREE->setTypeValidated(self::CHILD_FOLDING, 'Включён режим продакшн');
            return; //---
        }

        $foldings = $this->TREE->getChildsForValidate(self::CHILD_FOLDING, $group);

        $this->LOGGER->info(" ! Валидируем фолдинги");
        foreach ($foldings as $fUnique) {
            $this->LOGGER->info(" ! Проверяем на изменение фолдинг [$fUnique]");
            if (Handlers::getInstance()->getFoldingByUnique($fUnique)->checkFirstEntityChanged()) {
                //Выполним действия по оповещению об изменении
                $this->onFoldingChanged($fUnique);

                //Не будем проверять вообще все фолдинги, от которых мы зависим - это задача {@see ExternalProcess}.
                return; //---
            } else {
                $this->TREE->onChildValidated(self::CHILD_FOLDING, $fUnique);
            }
        }

        $this->LOGGER->info(" < Группа кешей [$group] прошла валидацию и не была изменена");
    }

    /**
     * Функция вызывается при изменении дочерней сущности.
     * Если мы уже были оповещены один раз об изменении сущности, то больше на
     * неё реагировать не будем.
     * Также в дереве валидации нужно вычистить всё, что касается изменённых сущностей.
     */
    private function onChildChanged($type, $child) {
        $affected = $this->TREE->onChildChanged($type, $child);
        foreach ($affected as $group) {
            $this->clean($group);
        }
        return $affected;
    }

    /**
     * Метод вызывается для оповещения кешей об изменении фолдинга.
     * Это очень важно, так как сущность фолдинга могла быть изменена, но после 
     * не была вызвана валидация группы и кеш так и не будет сброшен.
     */
    public function onFoldingChanged($foling) {
        return $this->onChildChanged(self::CHILD_FOLDING, $foling instanceof FoldedResources ? $foling->getUnique() : $foling);
    }

    public function onDbEntityChanged($child) {
        return $this->onChildChanged(self::CHILD_DBENTITY, $child);
    }

    /*
     * 
     * МЕТОДЫ
     * 
     */

    private function localCacheGroup($group) {
        check_condition($group, 'Cache group cannot be empty while using ' . __CLASS__);
        return $group . ' ';
    }

    private function localCacheKey($id, $group) {
        return $this->localCacheGroup($group) . "[$id]";
    }

    public function getFromCache($id, $group, /* array */ $REQUIRED_KEYS = null, $sign = null) {
        $cacheId = $this->localCacheKey($id, $group);

        //Сначала ищем в локальном хранилище
        if ($this->CACHE->has($cacheId)) {
            $CACHED = $this->CACHE->get($cacheId);
            if ($CACHED['sign'] == $sign) {
                $this->LOGGER->info("Информация по ключу '$cacheId' найдена в локальном кеше");
                return $CACHED['data'];
            } else {
                $this->LOGGER->info("Информация по ключу '$cacheId' найдена в локальном кеше, но старая и новая подписи не совпадают: [{}]!=[{}]. Чистим...", $CACHED['sign'], $sign);
                $this->CACHE->remove($cacheId);
                $this->CACHELITE->remove($id, $group);
                return null;
            }
        }

        /*
         * Самое интересное и спорное место всей реализации.
         * Нам нужно отслеживать свежесть кешей. Сами по себе они сбрасываются через опередённое время (время жизни кеша).
         * Но это время достаточно велико и, если мы сейчас, например, правим код, то нам некогда ждать, пока всё само сабой обновится.
         * 
         * Кеши валидируются через свои, so called, "подписи".
         * Например - структура проекта (строка навигации) зависит от кол-ва постов в каждом разделе, но при этом если какой-либо пост изменится, то
         * на подпись кеша для навигации это никак не повлияет, а ведь в посте мог измениться анонс, например.
         * 
         * Всё крутится вокруг изменения сущностей фолдингов. При изменении сущности будет сброшен кеш, который от этого фолдинга зависит.
         * Весь вопрос в том - как отслеживать эти изменения?.. Единственное решение - пробегать по всем фолдингам и выполнять checkAllEntityChanged.
         * Решение это довольно дорогостоящее. Даже если сущность не изменилась и кеш не будет перестроен, мы вынуждены выполнить очень много действий.
         * 
         * Есть два варинта для обеспечения "свежести" кешей:
         * 
         * Вариант №1.
         * Каждый раз при запросе кеша выполнять checkAllEntityChanged для фолдингов, от которых зависит запрашеваемая группа кешей.
         * Это нам ВСЕГДА обеспечит свежесть всех кешей, но данная операция является довольно тяжёлой.
         * 
         * Вариант №2.
         * Выполнять проверку checkAllEntityChanged для всех фолдингов, но так как это довольно дорого - делать это не каждый раз, а с определённой периодичностью.
         * Это также обеспечивает свежесть кешей, но операция - очень тяжёлая, поэтому её нельзя выполнять каждый раз.
         * В идеале её вообще должен выполнять внешний процесс, запускаемый раз в EXTERNAL_PROCESS_CALL_DELAY минут.
         * Будем эмулировать его работу посредством класса ExternalProcess.
         * В продакшене будет работать второй вариант - там частота обновлений кешей не так важна.
         */

        $this->validateGroup($group);


        PsProfiler::inst(__CLASS__)->start('LOAD from cache');
        $CACHED = $this->CACHELITE->get($id, $group);
        PsProfiler::inst(__CLASS__)->stop();

        if (!$CACHED) {
            $this->LOGGER->info("Информация по ключу '$cacheId' не найдена в кеше");
            return null;
        }

        if (!is_array($CACHED)) {
            $this->LOGGER->info("Информация по ключу '$cacheId' найдена в хранилище, но не является массивом. Чистим...");
            $this->CACHELITE->remove($id, $group);
            return null;
        }

        if (!array_has_all_keys(array('data', 'sign'), $CACHED)) {
            $this->LOGGER->info("Информация по ключу '$cacheId' найдена в хранилище, но отсутствует параметр sign или data. Чистим...");
            $this->CACHELITE->remove($id, $group);
            return null;
        }

        if ($CACHED['sign'] != $sign) {
            $this->LOGGER->info("Информация по ключу '$cacheId' найдена в хранилище, но старая и новая подписи не совпадают: [{}]!=[{}]. Чистим...", $CACHED['sign'], $sign);
            $this->CACHELITE->remove($id, $group);
            return null;
        }

        $MUST_BE_ARRAY = is_array($REQUIRED_KEYS);
        $REQUIRED_KEYS = to_array($REQUIRED_KEYS);
        if ($MUST_BE_ARRAY || !empty($REQUIRED_KEYS)) {
            //Если нам переданы ключи для проверки, значит необходимо убедиться, что сами данные являются массивом
            if (!is_array($CACHED['data'])) {
                $this->LOGGER->info("Информация по ключу '$cacheId' найдена в хранилище, но не является массивом. Чистим...");
                $this->CACHELITE->remove($id, $group);
                return null;
            }
            foreach ($REQUIRED_KEYS as $key) {
                if (!array_key_exists($key, $CACHED['data'])) {
                    $this->LOGGER->info("Информация по ключу '$cacheId' найдена, но в данных отсутствует обязательный ключ [$key]. Чистим...");
                    $this->CACHELITE->remove($id, $group);
                    return null;
                }
            }
        }

        $this->LOGGER->info("Информация по ключу '$cacheId' найдена в хранилище");
        //Перенесём данные в локальный кеш для быстрого доступа
        return array_get_value('data', $this->CACHE->set($cacheId, $CACHED));
    }

    public function saveToCache($object, $id, $group, $sign = null) {
        $cacheId = $this->localCacheKey($id, $group);
        $this->LOGGER->info("Информация по ключу '$cacheId' сохранена в кеш");

        $CACHED['sign'] = $sign;
        $CACHED['data'] = $object;

        //Нужно быть аккуратным - в cacheLite мы храним данные и подпись, а в local CACHE только данные
        PsProfiler::inst(__CLASS__)->start('SAVE to cache');
        $this->CACHELITE->save($CACHED, $id, $group);
        PsProfiler::inst(__CLASS__)->stop();
        return array_get_value('data', $this->CACHE->set($cacheId, $CACHED));
    }

    public function clean($group = null) {
        $this->LOGGER->info($group ? "Очистка кеша по группе [$group]" : 'Полная очистка кеша');
        $this->CACHELITE->clean($group);
        if ($group) {
            //Эту группу больше не нужно валидировать
            $this->TREE->setGroupValidated($group);
            //Очистим ключи локального хранилища
            $keys = $this->CACHE->keys();
            $removed = array();
            $prefix = $this->localCacheGroup($group);
            foreach ($keys as $key) {
                if (starts_with($key, $prefix)) {
                    $removed[] = $key;
                    $this->CACHE->remove($key);
                }
            }
            if ($removed) {
                $this->LOGGER->info('В локальном кеше были удалены следующие ключи: {}.', concat($removed));
            }
        } else {
            $this->CACHE->clear();
            $this->TREE->setAllValidated('Полная очистка кеша');
        }
    }

    /** @return PSCache */
    public static function inst() {
        return parent::inst();
    }

    protected function __construct() {
        $this->CACHE = new SimpleDataCache();
        $this->LOGGER = PsLogger::inst(__CLASS__);

        /**
         * Подключаем cache lite
         */
        ExternalPluginsManager::CacheLite();

        $liteOptions = array(
            'readControl' => true,
            'writeControl' => true,
            'readControlType' => 'md5',
            'automaticSerialization' => true, //Чтобы можно было писать объекты
            //
            'cacheDir' => DirManager::autogen('cache')->absDirPath(),
            'lifeTime' => CACHE_LITE_LIFE_TIME * 60,
            'caching' => true //Кеширование включено всегда
        );

        $this->CACHELITE = new Cache_Lite($liteOptions);

        $GROUPS = self::getCacheGroups();

        $TREE[self::CHILD_FOLDING] = Mappings::CACHE_FOLDINGS()->getAllMappedEntitys($GROUPS);
        $TREE[self::CHILD_DBENTITY] = Mappings::CACHE_DBENTITYS()->getAllMappedEntitys($GROUPS);

        $this->TREE = new PSCacheTree($this->LOGGER, $TREE);
    }

}

?>