<?php

/**
 * Основной класс для хранения данных из базы
 */
class BaseDataStore {

    private $_data;

    public function __construct(array $data) {
        $this->_data = $data;
    }

    protected function getData() {
        return $this->_data;
    }

    protected function _assertExists() {
        return true;
    }

    /**
     * Метод возвращает название класса.
     * Нужно для того, чтобы при фетчинге в объект результатов запроса передавать 
     * не просто текстовое название, а именно вызов этого метода.
     * Благодаря такому подходу далее можно будет увидеть использование данного 
     * класса при рефакторинге.
     */
    public static function getClass() {
        return get_called_class();
    }

    protected function hasKey($key) {
        return array_key_exists($key, $this->_data);
    }

    public function __set($property, $value) {
        $this->_data[$property] = $value;
    }

    public function __get($property) {
        check_condition(!$this->_assertExists() || array_key_exists($property, $this->_data), "Свойство $property не существует в бине " . get_called_class());
        return array_get_value($property, $this->_data);
    }

    public function __call($name, $args) {
        if (starts_with($name, 'get')) {
            $prefix = 'get';
        } else if (starts_with($name, 'is')) {
            $prefix = 'is';
        }

        check_condition(isset($prefix), "Вызван неизвестный метод $name бина " . get_called_class() . '.');

        $property = substr($name, strlen($prefix));
        $property{0} = strtolower($property{0});

        return $this->$property;
    }

    public function __toString() {
        return get_called_class() . ' data: ' . print_r($this->_data, true);
    }

}

?>
