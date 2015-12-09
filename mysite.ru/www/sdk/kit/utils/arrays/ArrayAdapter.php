<?php

/**
 * Удобная обёртка для работы с массивами
 */
class ArrayAdapter {

    /**
     * Данные, над которыми ведётся работа
     * 
     * @var array
     */
    private $data;

    /**
     * Признак редактируемости данных
     * 
     * @var bool
     */
    private $editable;

    /**
     * Массивы переопределений
     */
    private $storyValue = array();
    private $storyEmpty = array();

    /**
     * Метод получения данных
     * 
     * @return array
     */
    public function getData() {
        return $this->data;
    }

    /**
     * Метод утверждает, что данные в массиве могут быть отредактированы
     */
    private function assertEditable() {
        if (!$this->editable) {
            raise_error('Данные в массиве ' . get_called_class() . ' не могут быть отредактированы.');
        }
    }

    /**
     * Метод удаления ключей из массива
     */
    public function remove($keys) {
        $this->assertEditable();
        foreach (to_array($keys) as $key) {
            unset($this->data[$key]);
        }
    }

    /**
     * Метод установки значений в массив.
     * 
     * Моменты работы с историей:
     * 1. Изменять значение с историей можно даже, если редактирование массива запрещено.
     * 2. Если установлено значение с историей, то нельзя его менять без истории.
     * 3. В истории сохраняется только оригинальное значение, а не следующее историческое значение.
     * 
      $a = array('a' => 1, 'b' => 2, 'c' => 3);
      $ad = ArrayAdapter::inst($a);
      $ad->set('a', 2);
      $ad->set('a', 3, true);
      $ad->set('a', 4, true);
      $ad->restoreStory();
      $ad->set('a', 3);
      $ad->set('a', 4, true);
      $ad->set('a', 5, true);
      $ad->restoreStory();
      echo $ad;//-- В итоге a=3
     *
     * @param string $key - ключ
     * @param mixed $val - значение
     * @param bool $saveStory - признак установки значения с сохранением истории
     */
    public function set($key, $val, $saveStory = false) {
        $hasStory = array_key_exists($key, $this->storyValue) || array_key_exists($key, $this->storyEmpty);

        if ($saveStory) {
            if ($hasStory) {
                //Если история раньше уже была - не перезатираем её. нам важно именно оригинальное значение.
            } else {
                //Истории небыло, сохраняем её.
                if (array_key_exists($key, $this->data)) {
                    $this->storyValue[$key] = $this->data[$key];
                } else {
                    $this->storyEmpty[$key] = null;
                }
            }
        } else {
            if ($hasStory) {
                //Мы переустанавливаем значение, по которому ведётся история - нельзя так
                raise_error("Значение по ключу [$key] не может быть изменено, ведётся история изменений.");
            } else {
                //Простая установка значения, по которому нет истории.
                $this->assertEditable();
            }
        }

        $this->data[$key] = $val;
    }

    /**
     * Восстановление истории изменения параметров
     */
    public function restoreStory() {
        foreach ($this->storyValue as $key => $value) {
            $this->data[$key] = $value;
        }
        foreach ($this->storyEmpty as $key => $value) {
            unset($this->data[$key]);
        }
        $this->storyValue = array();
        $this->storyEmpty = array();
    }

    /**
     * 
     * @return ArrayAdapter
     */
    public function copy() {
        return ArrayAdapter::inst($this, false);
    }

    /*
     * KEYS
     */

    public function hasOneOf(array $keys) {
        return array_has_one_of_keys($keys, $this->data);
    }

    public function hasAll(array $keys) {
        return array_has_all_keys($keys, $this->data);
    }

    /**
     * Удаляет все значения, кроме тех, что указаны
     * @return ArrayAdapter
     */
    public function leaveKeys($keys) {
        $this->assertEditable();
        $keys = to_array($keys);
        foreach ($this->data as $key => $value) {
            if (!in_array($key, $keys)) {
                unset($this->data[$key]);
            }
        }
        return $this;
    }

    /**
     * Проверяет наличие всех ключей и непустоту всех значений по этим ключам 
     */
    public function hasAllNoEmpty($keys) {
        foreach (to_array($keys) as $key) {
            if (!$this->hasNoEmpty($key)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Возвращает первый ключ, значение которого не указано
     */
    public function getFirstEmpty($keys) {
        foreach (to_array($keys) as $key) {
            if ($key && !$this->hasNoEmpty($key)) {
                return $key;
            }
        }
        return null;
    }

    public function has($key) {
        return array_key_exists($key, $this->data);
    }

    public function hasNoEmpty($key) {
        return $this->has($key) && !isEmpty($this->data[$key]);
    }

    public function get($key, $default = null) {
        return value_Array($key, $this->data, $default);
    }

    public function int($key, $default = null) {
        $val = $this->get($key, $default);
        return is_inumeric($val) ? 1 * $val : null;
    }

    public function str($key, $default = null) {
        return trim($this->get($key, $default));
    }

    public function bool($key, $default = false) {
        return $this->has($key) ? !isEmpty($this->get($key)) : $default;
    }

    public function arr($key, $default = array()) {
        return to_array($this->get($key, $default));
    }

    //Возвращает элементы, ключи которых начинаются на префикс. Например: data-
    public function getByKeyPrefix($prefix, $prefixCut = false) {
        $result = array();
        foreach ($this->data as $key => $value) {
            if (starts_with($key, $prefix)) {
                $key = $prefixCut ? cut_string_start($key, $prefix) : $key;
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /*
     * VALUES
     */

    public function hasOneOfValue(array $values) {
        foreach ($values as $value) {
            if ($this->hasValue($value)) {
                return true;
            }
        }
        return false;
    }

    public function hasAllValue(array $values) {
        foreach ($values as $value) {
            if (!$this->hasValue($value)) {
                return false;
            }
        }
        return true;
    }

    public function hasValue($value) {
        return in_array($value, $this->data);
    }

    public function dataToString($assoc = true) {
        return array_to_string($this->data, $assoc);
    }

    public function __toString() {
        return $this->dataToString();
    }

    /*
     * СИНГЛТОН
     */

    private static $insts = array();

    /**
     * Основной метод, создающий экземпляр класса - адаптера.
     * 
     * Нужно быть аккуратным с передачей массива по ссылке. Вот такой код приведёт к ошибке:
     * $params = ArrayAdapter::inst($params, true);
     * так как теперь внутри нового созданного ArrayAdapter поле $data указывает на сам же ArrayAdapter, 
     * а не на исходный массив $params.
     * 
     * @param array|ArrayAdapter $data - данные
     * @param bool $byRef - признак передачи массива по ссылке. Тогда можно его модифицировать и влиять на переданный массив.
     * @return ArrayAdapter
     */
    public static function inst(/* array or ArrayAdapter */ &$data = array(), $byRef = false, $editable = true) {
        $className = get_called_class();

        if ($className == __CLASS__) {
            //Передан другой ArrayAdapter
            if ($data instanceof ArrayAdapter) {
                return $byRef ? new ArrayAdapter($data->data, $editable) : new ArrayAdapter($array = $data->data, $editable);
            }
            //Передан массив
            return $byRef ? new ArrayAdapter($data, $editable) : new ArrayAdapter($array = $data, $editable);
        }

        if (array_key_exists($className, self::$insts)) {
            return self::$insts[$className];
        }

        PsLogger::inst(__CLASS__)->info("Created instance of $className");

        return self::$insts[$className] = $byRef ? new $className($data, $editable) : new $className($array = $data, $editable);
    }

    protected final function __construct(array &$array, $editable) {
        $this->data = &$array;
        $this->editable = $editable;
    }

}

?>
