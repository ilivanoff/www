<?php

/**
 * Класс - основа для всех контекстов.
 * Контекст представляет собой: идентификато контекста + объект контекста.
 * Повторная установка контекста с тем-же идентификатором запрещена.
 * Каждый вновь устанавливаемый контекст получает свой уникальный номер в рамках данного класса и к этому номеру привязываются: 
 * идентификатор контекста, объект контекста, данные контекста (которые будут установлены в процессе работы с контекстом).
 */
abstract class AbstractContext extends AbstractSingleton {

    /**
     * Наблюдатели за контекстом
     */
    private static function watchers() {
        return array(FoldedContextWatcher::getInstance());
    }

    //Название класса
    protected $CLASS;

    /** @var PsLoggerInterface */
    protected $LOGGER;
    //Номер контекста
    private $ctxtNum = 0;
    //Номер контекста->Идентификатор контекста
    private $ctxtNum2Ident = array();
    //Номер контекста->Объект контекста
    private $ctxtNum2Object = array();
    //Номер контекста->Данные контекста
    private $ctxtNum2Data = array();

    public function setContext($contextIdent, $contextObject = null) {
        $descr = $this->CLASS;
        check_condition($contextIdent, "$descr: context ident cannot be empty");
        check_condition(!$this->hasContext($contextIdent), "$descr: context [$contextIdent] is already set");
        check_condition(!$this->isVirtualContext(), "$descr: context [$contextIdent] cannot be set - virtual context is set");

        ++$this->ctxtNum;
        $this->ctxtNum2Ident[$this->ctxtNum] = $contextIdent;
        $this->ctxtNum2Object[$this->ctxtNum] = $contextObject;
        $this->ctxtNum2Data[$this->ctxtNum] = array();

        self::notifyWatchers($this, true);
    }

    public function dropContext() {
        self::notifyWatchers($this, false);

        $descr = $this->CLASS;
        check_condition($this->ctxtNum > 0, "$descr: context cannot be dropped - context is not set");
        check_condition(!$this->isVirtualContext(), "$descr: context cannot be dropped - virtual context is set");

        unset($this->ctxtNum2Data[$this->ctxtNum]);
        unset($this->ctxtNum2Object[$this->ctxtNum]);
        unset($this->ctxtNum2Ident[$this->ctxtNum]);
        --$this->ctxtNum;
    }

    /**
     * Проверка, установлен ли контекст
     * 
     * @param type $ident - идентификатор контекста (для проверки того, что установлен контекст именно с этим идентификатором)
     */
    public function isSetted($ident = null) {
        return $this->ctxtNum > 0 && (!$ident || $this->ctxtNum2Ident[$this->ctxtNum] == $ident);
    }

    /**
     * Утверждение того, что контекст установлен
     */
    public function assertSetted() {
        check_condition($this->isSetted(), $this->CLASS . ' - context is not set');
    }

    /**
     * Проверка, имеется ли среди установленных контекстов контекст с переданным идентификатором
     */
    public function hasContext($ident) {
        return in_array($ident, $this->ctxtNum2Ident);
    }

    /**
     * Метод возвращает текущий номер контекста, при этом учитывая, что может быьт установлен виртуальный контекст
     */
    private function curCtxtNum() {
        if ($this->isVirtualContext()) {
            return $this->virtualCtxtNum;
        } else {
            $this->assertSetted();
            return $this->ctxtNum;
        }
    }

    /*
     * Виртуальный контекст - это всего лишь временная установка одного из ранее установленных контекстов, как текущего.
     * Работа должна выполняться по следующему плану:
     * 
     * 1. Устанавливаем виртуальный контекст
     * 2. Устанавливаем параметры
     * 3. Дропаем виртуальный контекст
     */

    private $virtualCtxtNum;

    private function isVirtualContext() {
        return isset($this->virtualCtxtNum);
    }

    public function setVirtualContext($ident) {
        check_condition(!$this->isVirtualContext(), 'Virtual context is already set');

        foreach ($this->ctxtNum2Ident as $ctxtNum => $ctxtIdent) {
            if ($ctxtIdent == $ident) {
                $this->virtualCtxtNum = $ctxtNum;
                break;
            }
        }

        check_condition($this->isVirtualContext(), "Can`t set virtual context - context [$ident] is not setted");
    }

    public function dropVirtualContext() {
        check_condition($this->isVirtualContext(), 'Can`t drop virtual context - virtual context is not set');
        unset($this->virtualCtxtNum);
    }

    /*
     * ====================================
     * = ПОЛУЧЕНИЕ ИНФОРМАЦИИ О КОНТЕКСТЕ =
     * ====================================
     */

    public function getContext() {
        return $this->ctxtNum2Object[$this->curCtxtNum()];
    }

    public function getContextIdent() {
        return $this->ctxtNum2Ident[$this->curCtxtNum()];
    }

    public function getParams() {
        return $this->ctxtNum2Data[$this->curCtxtNum()];
    }

    public function hasParam($key) {
        return array_key_exists($key, $this->ctxtNum2Data[$this->curCtxtNum()]);
    }

    public function getParam($key, $default = null) {
        return value_Array($key, $this->ctxtNum2Data[$this->curCtxtNum()], $default);
    }

    public function setParam($key, $value) {
        $this->ctxtNum2Data[$this->curCtxtNum()][$key] = $value;
    }

    public function addParam($key, $value) {
        $this->ctxtNum2Data[$this->curCtxtNum()][$key][] = $value;
    }

    public function setMappedParam($key, $mapKey, $mapValue) {
        $this->ctxtNum2Data[$this->curCtxtNum()][$key][$mapKey] = $mapValue;
    }

    public function setMappedParam2($key, $mapKey, $mapKey2, $mapValue) {
        $this->ctxtNum2Data[$this->curCtxtNum()][$key][$mapKey][$mapKey2] = $mapValue;
    }

    public function setMappedParams($key, array $k_v) {
        foreach ($k_v as $mapKey => $mapValue) {
            $this->setMappedParam($key, $mapKey, $mapValue);
        }
    }

    public function hasMappedParam($key, $mapKey) {
        return $this->hasParam($key) && array_key_exists($this->ctxtNum2Data[$this->curCtxtNum()][$key], $mapKey);
    }

    public function getMappedParam($key, $mapKey, $default = null) {
        return value_Array($mapKey, $this->getParam($key), $default);
    }

    public function getMappedParam2($key, $mapKey, $mapKey2, $default = null) {
        return array_get_value_in(array($mapKey, $mapKey2), $this->getParam($key));
    }

    public function resetParam($key) {
        unset($this->ctxtNum2Data[$this->curCtxtNum()][$key]);
    }

    public function __toString() {
        return $this->CLASS . ' (' . ($this->isSetted() ? $this->getContextIdent() : 'not setted') . ')';
    }

    /*
     * Работа с параметрами
     */

    public function getNumAndIncrease($key) {
        $curNum = $this->getParam($key, 0);
        ++$curNum;
        $this->setParam($key, $curNum);
        return $curNum;
    }

    public function dump() {
        if (!$this->isSetted()) {
            echo 'Context not setted';
            br();
            br();
            return;
        }
        for ($index = 1; $index <= $this->ctxtNum; $index++) {
            $ident = $this->ctxtNum2Ident[$index];
            $context = $this->ctxtNum2Object[$index];

            echo "$index. Context: $ident";
            br();
            echo 'Context data: ';
            var_dump($context);
            br();
            foreach ($this->ctxtNum2Data[$index] as $key => $value) {
                echo "$key => ";
                var_dump($value);
                br();
            }
            br();
            br();
        }
    }

    /**
     * НАБЛЮДАТЕЛИ
     */
    private static function notifyWatchers(AbstractContext $ctxt, $isSetted) {
        /* @var $watcher AbstractContextWatcher */
        foreach (self::watchers() as $watcher) {
            $watcher->ctxtAction($ctxt, $isSetted);
        }
    }

    /**
     * Конструктор
     */
    final protected function __construct() {
        $this->CLASS = get_called_class();
        $this->LOGGER = PsLogger::inst($this->CLASS);
    }

}

?>