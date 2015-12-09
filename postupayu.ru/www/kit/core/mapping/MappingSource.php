<?php

/**
 * Источник данных для мапинга.
 * Его хэш-код складывается из названия класса, параметров инициализации и идентификатора маппинга.
 *
 * @author azazello
 */
abstract class MappingSource {

    /** Название класса-имплементации */
    private $CLASS;

    /** @var PsLoggerInterface */
    private $LOGGER;

    /** Уникальный хэш-код самого источника */
    private $HASH;

    /** Параметры построения источника */
    private $PARAMS;

    /** Идентификатор маппинга */
    private $MIDENT;

    /** Признак проинициализированности */
    private $_preloaded = false;

    /**
     * Экземпляр каждого источника данных может быть создан только в единственном экземпляре
     */
    private static $items = array();

    /** @return MappingSource */
    public static final function inst(array $params, $mident) {
        //Получим название класса-имплементации
        $class = get_called_class();

        //Отсортируем параметры
        ksort_deep($params);

        //Получим hash загрузчика данных
        $hash = simple_hash(array('class' => $class, 'params' => $params, 'mident' => $mident));

        if (!array_key_exists($hash, self::$items)) {
            self::$items[$hash] = new $class($class, $params, $mident, $hash);
        }

        return self::$items[$hash];
    }

    /**
     * Конструктор у класса-источника может быть только один
     */
    private final function __construct($class, array $params, $mident, $hash) {
        $this->CLASS = $class;

        $this->LOGGER = PsLogger::inst($class);

        $this->HASH = $hash;
        $this->MIDENT = $mident;
        $this->PARAMS = $params;

        check_condition($this->HASH, 'Уникальный код источника не может быть пустым');
        check_condition($this->MIDENT, 'Идентификатор маппинга не может быть пустым');
        check_condition(is_array($this->PARAMS), 'Параметры построения источника должны быть массивом');

        $this->init($mident, $params);
    }

    /** @return MappingSource */
    private final function doPreload() {
        if (!$this->_preloaded) {
            $this->_preloaded = true;
            $this->preload($this->MIDENT, $this->PARAMS);
        }
        return $this;
    }

    public final function getIdentsLeft() {
        return array_values(array_unique($this->doPreload()->loadIdentsLeft($this->MIDENT, $this->PARAMS)));
    }

    public final function getIdentsRight(MappingSource $srcLeft, $lident) {
        return array_values(array_unique($this->doPreload()->loadIdentsRight($this->MIDENT, $this->PARAMS, $srcLeft, $lident)));
    }

    public final function getDescription() {
        return $this->loadDescription($this->MIDENT, $this->PARAMS);
    }

    protected abstract function init($mident, array $params);

    protected abstract function preload($mident, array $params);

    protected abstract function loadIdentsLeft($mident, array $params);

    protected abstract function loadIdentsRight($mident, array $params, MappingSource $srcLeft, $lident);

    protected abstract function loadDescription($mident, array $params);

    /**
     * Хэш - код источника
     */
    public final function getHash() {
        return $this->HASH;
    }

    /**
     * Параметры источника
     */
    public function getParams() {
        return $this->PARAMS;
    }

    /**
     * Идентификатор маппинга
     */
    public function getMident() {
        return $this->MIDENT;
    }

    /**
     * Приведение к строке
     */
    public final function __toString() {
        return $this->CLASS . array_to_string(array('mident' => $this->MIDENT, 'params' => $this->PARAMS));
    }

}

?>