<?php

final class Secundomer {

    const ROUND = 3;

    private $working = false;
    /*
     * Статистика
     */
    private $time = 0;
    private $totalTime = 0;
    private $callsCount = 0;
    private $descr;

    private function __construct($descr) {
        $this->descr = $descr;
    }

    /** @return Secundomer */
    public static function inst($descr = '') {
        return new Secundomer($descr);
    }

    /** @return Secundomer */
    public static function startedInst($descr = '') {
        return self::inst($descr)->start();
    }

    /**
     * Очистка секундомера
     */
    public function clear() {
        check_condition(!$this->working, 'Secundomer is working now');
        $this->time = 0;
        $this->totalTime = 0;
        $this->callsCount = 0;
    }

    /**
     * Запуск секундомера.
     * Сразу запоминаем метку времени, чтобы максимально точно подситать время работы.
     * 
     * @return Secundomer
     */
    public function start() {
        check_condition(!$this->working, "Secundomer $this->descr is working now");
        $this->working = true;

        $this->time = microtime(true);

        return $this;
    }

    /**
     * Остановка секундомера.
     * Вернём ссылку на экземпляр, чтобы с ней можно было сразу работать.
     * 
     * @param bool $save - признак, сохранять ли результаты. Если возникла ошибка, то не нужно сохранять.
     * @return Secundomer
     */
    public function stop($save = true) {
        $time = microtime(true);
        check_condition($this->working, "Secundomer $this->descr is not started");

        if ($save) {
            $this->time = max(array($time - $this->time, 0));
            $this->totalTime += $this->time;
            ++$this->callsCount;
        }

        $this->working = false;

        return $this;
    }

    /**
     * Время выполнения последнего секундомера (в секундах)
     */
    public function getTime() {
        check_condition(!$this->working, 'Secundomer is working now');
        return $this->time;
    }

    /**
     * Полное время выполнения секундомеров (в секундах)
     */
    public function getTotalTime() {
        check_condition(!$this->working, 'Secundomer is working now');
        return $this->totalTime;
    }

    public function getTotalTimeRounded() {
        return round($this->getTotalTime(), self::ROUND);
    }

    public function getCount() {
        check_condition(!$this->working, 'Secundomer is working now');
        return $this->callsCount;
    }

    /**
     * Добавляет статистику данного секундомера
     * 
     * @return Secundomer
     */
    public function add($callsCount, $totalLime) {
        $this->totalTime += $totalLime;
        $this->callsCount += $callsCount;
        return $this;
    }

    /**
     * Добавляет к статистике данного секундомера - другой секундомер
     * 
     * @return Secundomer
     */
    public function addSecundomer(Secundomer $secundomer) {
        return $this->add($secundomer->callsCount, $secundomer->totalTime);
    }

    /**
     * Среднее время выполнения секундомера (в секундах)
     */
    public function getAverage() {
        return $this->callsCount > 0 ? round($this->totalTime / $this->callsCount, self::ROUND) : 0;
    }

    public function stopAndGetAverage() {
        return $this->stop()->getAverage();
    }

    public function isStarted() {
        return $this->working;
    }

    public function __toString() {
        return "Count: {$this->callsCount}, total: {$this->getTotalTimeRounded()}" . ($this->callsCount > 1 ? ", average: {$this->getAverage()}" : '');
    }

}

?>