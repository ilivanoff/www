<?php

/**
 * Класс акции - инициализируется на основе данных в блоке
 * {stock name='Загадочное животное' type='mosaic' to='2013-06-30 11:25:00+04:00' active='1' data_id='1'}
 * 
 * Инициализация проходит в два этапа:
 * 1. На основе параметра ident получаем класс фолдинга, соответствующий акции.
 * 2. Инициализируем акцию, передав параметры из Smarty-функции, пример которой приведён выше.
 */
abstract class BaseStock extends FoldedClass {

    protected function _construct() {
        //do nothing...
    }

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    /** @var ArrayAdapter */
    private $params;

    /** Параметры акции из смарти-блока */
    private $name;
    private $isActive;
    private $isByDate;
    private $secondsLeft;

    /** Полный идентификатор (с параметрами) */
    private $stockIdent;

    public final function init(ArrayAdapter $params) {
        //Стандартные параметры
        $this->name = $params->str('name', 'Акция');
        $this->isActive = $params->bool('active', true);
        $this->isByDate = $params->hasNoEmpty('to');
        if ($this->isByDate) {
            $this->secondsLeft = strtotime($params->str('to')) - time();
            $this->isActive = $this->isActive && ($this->secondsLeft > 0);
        }

        //Параметры акции (только те, что начинаются с data_)
        $this->params = ArrayAdapter::inst($params->getByKeyPrefix('data_', true));

        //Полный идентификатор акции
        $this->stockIdent = $this->sign($this->params->getData());

        //Вызовем инициализацию класса-наследника
        $this->onInit($this->params);
    }

    protected abstract function onInit(ArrayAdapter $stockParams);

    public function isIt($type, array $params) {
        return ($type == self::getType()) && ($this->stockIdent == $this->sign($params));
    }

    /**
     * Идентификатор акции - хэш от типа акции и её параметров.
     * Две акции с одним типом но разными кодами считаются разными.
     * 
     * @param array $params - параметры акции
     */
    private function sign(array $params) {
        $type = self::getType();
        $sign['_STOCK_CLASS_TYPE_'] = $type;
        foreach (PsUtil::getClassConsts($this, 'DATA_') as $param) {
            $sign[$param] = check_condition(array_get_value($param, $params), "Для акции $type не указан обязательный параметр [$param].");
        }
        return simple_hash($sign);
    }

    /**
     * Название акции
     */
    public function getName() {
        return $this->name;
    }

    /**
     * html - короткий вид акции, отображаемый в загружаемом окне.
     */
    public abstract function getShortView();

    /**
     * Полный вид акции, открываемый в отдельном окне.
     * @return StockViewData
     */
    public abstract function getFullView();

    /**
     * Тип акции. Необходим для определения класса фолдинга по названию в смарти-теге
     */
    public static function getType() {
        return self::getIdent();
    }

    /**
     * Идентификатор акции, строемый на основе идентификатора акции и её параметров
     */
    public function getStockIdent() {
        return $this->stockIdent;
    }

    /**
     * Признак - ограничена ли акция по времени. Если так - для акции будет отображён секундмер обратного отчёта.
     */
    public function isByDate() {
        return $this->isByDate;
    }

    /**
     * Для акций, ограниченных по времени, возвращает кол-во секунд, оставшихся до окончания акции
     */
    public function getSecondsLeft() {
        return $this->secondsLeft;
    }

    /**
     * Признак - активная ли акция.
     * Она активна, если не указано форсированно {... active='0'} или если дата окончания акции не прошла.
     */
    public function isActive() {
        return $this->isActive;
    }

    /**
     * Ссылка для открытия акции в отдельном окне
     */
    public function popup() {
        return PopupPagesManager::inst()->getPageUrl(PP_stock::getIdent(), array(STOCK_IDENT_PARAM => $this->stockIdent));
    }

    /**
     * Сущность фолдинга для данного типа акций
     * 
     * @return FoldedEntity
     */
    protected function foldedEntity() {
        return StockManager::inst()->getFoldedEntity(self::getType());
    }

    protected function getInfo($tplPath, array $smartyParams = array()) {
        return $this->foldedEntity()->getInfo($tplPath, $smartyParams);
    }

    /**
     * УТИЛИТНАЯ ФУНКЦИЯ
     * Для фетчинга короткого вида акции
     * 
     * @param array $smartyParams - параметры Smarty
     */
    protected function fetchShort(array $smartyParams = array()) {
        $smartyParams['stock'] = $this;
        $smartyParams['showcase_mode'] = true;
        return $this->foldedEntity()->fetchTpl($smartyParams);
    }

    /**
     * УТИЛИТНАЯ ФУНКЦИЯ
     * Для фетчинга полного вида акции
     * 
     * @param array $smartyParams - параметры Smarty
     */
    protected function fetchFull(array $smartyParams = array()) {
        $smartyParams['stock'] = $this;
        $smartyParams['showcase_mode'] = false;
        return $this->foldedEntity()->fetchTplWithResources($smartyParams);
    }

    public function __toString() {
        return $this->getName() . ' (' . self::getType() . '/' . $this->getStockIdent() . ')';
    }

}

?>