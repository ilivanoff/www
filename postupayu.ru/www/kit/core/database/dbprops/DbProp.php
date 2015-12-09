<?php

/**
 * Настройки, хранимые в базе. Обычно их очень мало и они объявлены в виде
 * статических методов данного класса, поэтому мы не будем заморачиваться с кешированием 
 * их локально и будем напрямую работать с базой.
 * 
 * TODO - выкинуть!!!
 */
final class DbProp extends PsEnum {

    /**
     * Время последнего обновления настроек экспорта
     * @return DbProp
     */
    public static final function DB_SETTINGS() {
        return self::inst(DbPropType::INT(), 0);
    }

    /**
     * Время последнего обновления настроек экспорта
     * @return DbProp
     */
    public static final function TEST() {
        return self::inst(DbPropType::INT(), 10);
    }

    /**
     * Время последнего обновления настроек экспорта
     * @return DbProp
     */
    public static final function TESTB() {
        return self::inst(DbPropType::BOOL());
    }

    /**
     * Тип свойства БД
     * 
     * @var DbPropType
     */
    private $type;

    /**
     * Значение поумолчанию
     * 
     * @var mixed
     */
    private $default;

    protected function init(DbPropType $type = null, $default = null) {
        $this->type = PsCheck::object($type);
        $this->default = $type->validateDefault($default);
    }

    /**
     * Значение поумолчанию
     */
    public function getDefault() {
        return $this->default;
    }

    /**
     * Получение значения настройки
     */
    public function get() {
        return $this->type->get($this);
    }

    /**
     * Установка нового значения настройки
     */
    public function set($val) {
        return $this->type->set($this, $val);
    }

    /**
     * Удаление настройки из базы
     */
    public function reset() {
        UtilsBean::inst()->delDbProp($this->name());
    }

}

?>