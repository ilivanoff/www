<?php

/**
 * Класс отвечает за корректное завершение работы.
 * 
 * Добавлять сюда следует только те классы, в порядке закрытия которых мы должны быть уверены,
 * в остальных случаях можно использовать __destruct().
 * 
 * Для нас важно, например, чтобы логгер был закрыт после профайлера, так как профайлер перед закрытием
 * выполняет ряд действий и пишет об этом в лог.
 * 
 * Класс может быть расширен проектным PsShotdown, в котором будут определены свои константы
 */
class PsShotdownSdk {
    //Порядок закрытия

    const FoldedResourcesManager = 100;
    const TestDestructable = 666; //Для тестового класса
    const PsLock = 997;
    const PsProfiler = 998;
    const PsLogger = 999; //!ВСЕГДА! Должен быть последним

    private static $DESTRUCTS = null;

    /**
     * Основной метод, регистрирующий класс для закрытия
     * 
     * @param Destructable $inst - Экземпляр класса, которй будет гарантированно закрыт в свою очередь
     * @param int $order - порядок закрытия
     */
    public static function registerDestructable(Destructable $inst, $order) {
        /*
         * Класс получаем через get_called_class(), так как PsShotdownSdk может быть переопределён проектным
         */
        $class = get_called_class();
        /*
         * Проверяем, что нам передан валидный order
         */
        $order = PsUtil::assertClassHasConstVithValue($class, null, PsCheck::int($order));
        /*
         * Регистрируем shutdown
         */
        if (is_null(self::$DESTRUCTS)) {
            PsUtil::assertClassHasDifferentConstValues($class);
            self::$DESTRUCTS = array();
            register_shutdown_function(array(__CLASS__, '_doShotdown'));
        }
        /*
         * Проверим, что нет попытки повторной регистрации с тем-же order
         */
        if (array_key_exists($order, self::$DESTRUCTS)) {
            raise_error("Попытка повторно зарегистрировать Destructable с порядком [$order] для класса " . get_class($inst));
        }
        /*
         * Регистрируем класс на закрытие
         */
        self::$DESTRUCTS[$order] = $inst;
    }

    /**
     * Функция, выполняющая закрытие классов.
     * Должна быть именно public, иначе не будет вызвана!
     */
    public static function _doShotdown() {
        PsCheck::arr(self::$DESTRUCTS);

        ksort(self::$DESTRUCTS);

        /* @var $inst Destructable */
        foreach (self::$DESTRUCTS as $ord => $inst) {
            //Пишем в логгер до закрытия класса, так как логгер закрывается последним
            PsLogger::inst(__CLASS__)->info($ord . '. ' . get_class($inst) . ' - desctucted');
            $inst->onDestruct();
        }

        self::$DESTRUCTS = null;
    }

}

?>