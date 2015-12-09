<?php

/**
 * Базовый класс для всех синглтонов
 *
 * @author azazello
 */
abstract class AbstractSingleton {

    private static $_insts_ = array();
    private static $_instsrq_ = array();

    /** @var Secundomer */
    private static $_secundomer_;

    /**
     * Основной метод получения экземпляра.
     * 
     * @param type $silentOnDoubleTry - признак, стоит ли нам ругаться, если мы 
     * обнаруживаем зацикливание при попытке получения экземпляра класса.
     * 
     * Это нужно для классов, которые выполняют сложную логику в конструкторе, которая
     * может привести к повторному вызову ::inst() внутри этого конструктора.
     * 
     * Классы, которые используют эту возможность:
     * @link DbChangeListener - менеджер прослушивания изменений в БД
     */
    protected static function inst($silentOnDoubleTry = false) {
        $class = get_called_class();
        if (array_key_exists($class, self::$_insts_)) {
            return self::$_insts_[$class];
        }

        if (array_key_exists($class, self::$_instsrq_)) {
            if ($silentOnDoubleTry) {
                return null;
            }
            raise_error("Double try to get singleton of [$class]");
        }
        self::$_instsrq_[$class] = true;

        //Создаём экземпляр
        $sec = Secundomer::startedInst("Creating singleton of $class");
        self::$_insts_[$class] = new $class();
        $sec->stop();

        //Экземпляр успешно создан
        unset(self::$_instsrq_[$class]);

        //Теперь добавим в профайлер. Всё это нужно для защиты от зацикливания.
        PsProfiler::inst(__CLASS__)->add($class, $sec);

        //Добавим к глобальному секундомеру - текущий
        $SECUNDOMER = self::$_secundomer_ ? self::$_secundomer_ : self::$_secundomer_ = Secundomer::inst();
        $SECUNDOMER->addSecundomer($sec);

        //Отлогируем
        PsLogger::inst(__CLASS__)->info("+ $class ({$sec->getAverage()} / {$SECUNDOMER->getTotalTimeRounded()})");
        return self::$_insts_[$class];
    }

    protected function __construct() {
        //Конструктор будет реализован в наследнике. При желании он может быть переопределён
    }

}

?>