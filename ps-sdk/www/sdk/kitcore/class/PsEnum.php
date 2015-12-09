<?php

/**
 * Базовый класс для енумов
 *
 * @author azazello
 */
abstract class PsEnum {

    private static $_INSTS_ = array();
    private static $_VALUES_ = array();
    private static $_NOW_CREATE_ = null;

    /**
     * Название енума VAL1
     */
    private $name;

    /**
     * Идентификатор енума MyEnum::VAL1
     */
    private $unique;

    /**
     * Функция инициализации енума
     */
    protected abstract function init();

    /**
     * Конструктор енумов не вызывает никакой функциональности родительского класса
     */
    final function __construct($unique, $name) {
        check_condition($unique === self::$_NOW_CREATE_, 'Недопустимо создавать объекты ' . get_called_class() . ' напрямую.');
        $this->name = $name;
        $this->unique = $unique;
    }

    /**
     * Основной метод получения экземпляра енума
     */
    protected static function inst() {
        $trace = debug_backtrace(0, 2)[1];
        //Из стека получим информацию о классе, функции и аргументах вызова
        $class = $trace['class'];
        //$trace = PsUtil::getClassFirstCall($class);
        $name = $trace['function'];
        $unique = "$class::$name";
        //Проверим в кеше
        if (array_key_exists($unique, self::$_INSTS_)) {
            return self::$_INSTS_[$unique];
        }
        //Устанавливаем признак создания экземпляра
        self::$_NOW_CREATE_ = $unique;
        //Аргументы вызова
        $args = func_get_args();
        //Создаём экземпляр
        $inst = new $class($unique, $name);
        //Сбросим признак
        self::$_NOW_CREATE_ = null;
        //Сохраняем его в кеш
        self::$_INSTS_[$unique] = $inst;
        //Вызовем функцию init для наполнения полей класса
        call_user_func_array(array($inst, 'init'), $args);
        //Возвращаем экземпляр
        return $inst;
    }

    public static final function names() {
        return PsUtil::getClassMethods(get_called_class(), true, true, true, false, __CLASS__);
    }

    public static function valueOf($name) {
        if (in_array($name, self::names())) {
            return static::$name();
        }
        PsUtil::raise('Enum {} not contains value {}', get_called_class(), $name);
    }

    public static final function values() {
        $class = get_called_class();
        if (!array_key_exists($class, self::$_VALUES_)) {
            check_condition($class != __CLASS__, "Invalid context: [$class]");
            self::$_VALUES_[$class] = array();
            foreach (self::names() as $name) {
                self::$_VALUES_[$class][] = $class::$name();
            }
        }
        return self::$_VALUES_[$class];
    }

    public final function name() {
        return $this->name;
    }

    public final function unique() {
        return $this->unique;
    }

    public function __toString() {
        return $this->name;
    }

}

?>