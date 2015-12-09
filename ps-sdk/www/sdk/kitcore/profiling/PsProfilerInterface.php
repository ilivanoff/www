<?php

abstract class PsProfilerInterface {

    private $num = 0;               //Номер текущего профайлера
    private $idents = array();      //Карта: номер идентификатора -> идентификатор
    private $secundomers = array(); //Карта: идентификатор -> секундомер
    private $profilerId;            //Код профайлера для передачи в описание запускаемым секундомерам

    public final function __construct($profilerId) {
        $this->profilerId = $profilerId;
    }

    /**
     * Метод регистрирует новый секундомер для идентификатора
     * 
     * @return Secundomer зарегистрированный идентификатор
     */
    private final function registerSecundomer($ident) {
        if (!array_key_exists($ident, $this->secundomers)) {
            $this->secundomers[$ident] = Secundomer::inst($this->profilerId . '::' . $ident);
        }
        return $this->secundomers[$ident];
    }

    /**
     * Запуск профилирования
     * 
     * @param type $ident
     */
    public function start($ident) {
        $this->idents[++$this->num] = $ident;
        return $this->registerSecundomer($ident)->start();
    }

    /**
     * Остановка профилирования
     * 
     * @return Secundomer последний запушеный секундомер
     */
    public function stop($save = true) {
        check_condition($this->num > 0, 'Не установлен текущий идентификатор профилирования для ' . $this->profilerId);
        $secundomer = $this->secundomers[$this->idents[$this->num]]->stop($save);
        unset($this->idents[$this->num]);
        --$this->num;
        return $secundomer;
    }

    /**
     * Добавление к профайлеру статистики
     * 
     * @param type $ident - идентификатор профилирования
     * @param Secundomer $secundomer
     */
    public function add($ident, Secundomer $secundomer) {
        return $this->registerSecundomer($ident)->addSecundomer($secundomer);
    }

    /**
     * Признак - работает ли данный профайлер.
     * В отличае от логгера в случае глобального включения профайлеров,
     * какой-то конкретный всёже может не работать, если он достиг максимального размера, например.
     */
    public abstract function isEnabled();

    /**
     * Статистика - массив (код профилирования => Secundomer)
     */
    public function getStats() {
        return $this->secundomers;
    }

}

?>