<?php

class SimpleDataCache {

    public $data = array();

    public function setAll(array $data) {
        $this->data = array_merge($this->data, $data);
    }

    public function set($key, $value = null) {
        return $this->data[$key] = $value;
    }

    public function get($key) {
        return $this->has($key) ? $this->data[$key] : null;
    }

    public function keys() {
        return array_keys($this->data);
    }

    public function has($key) {
        return array_key_exists($key, $this->data);
    }

    public function isArray($key) {
        return $this->has($key) && is_array($this->data[$key]);
    }

    public function remove($key) {
        if ($this->has($key)) {
            unset($this->data[$key]);
        }
    }

    public function count() {
        return count($this->data);
    }

    public function isEmpty() {
        return $this->count() == 0;
    }

    public function clear() {
        $this->data = array();
    }

    /**
     * Синглтон
     */
    private static $insts = array();

    /**
     * Метод возвращает экземпляр кеша. Если хоть один из ключей не null, будет
     * возвращён кешированный экземпляр.
     * 
     * @param type $ident1 - ключ 1 для построения кеша
     * @param type $ident2 - ключ 2 для построения кеша
     * @return SimpleDataCache
     */
    public static function inst($ident1 = null, $ident2 = null) {
        if (!$ident1 && !$ident2) {
            return new SimpleDataCache();
        }
        $key = strval($ident1) . '::' . strval($ident2);
        return array_key_exists($key, self::$insts) ? self::$insts[$key] : self::$insts[$key] = new SimpleDataCache();
    }

}

?>