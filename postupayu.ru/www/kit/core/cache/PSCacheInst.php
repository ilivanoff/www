<?php

/**
 * Специальный класс-обёртка для работы над группой кешей.
 *
 * @author azazello
 */
class PSCacheInst {

    private $group;
    private $sign;

    private function __construct($group, $sign) {
        $this->group = $group;
        $this->sign = $sign;
    }

    private static $insts = array();

    /**
     * Основной метод, возвращающий экземпляры оболочек над группами кешей
     * @return PSCacheInst
     */
    public static function inst($group, $sign = null) {
        $key = "G:$group;S:$sign";
        if (!array_key_exists($key, self::$insts)) {
            check_condition(in_array($group, PSCache::getCacheGroups()), "Неизвестная группа кеширования [$group].");
            self::$insts[$key] = new PSCacheInst($group, $sign);
        }
        return self::$insts[$key];
    }

    private function getSign($sign) {
        return $this->sign === null ? $sign : $this->sign . ($sign === null ? '' : ":$sign");
    }

    public function getFromCache($id, /* array */ $REQUIRED_KEYS = null, $sign = null) {
        return PSCache::inst()->getFromCache($id, $this->group, $REQUIRED_KEYS, $this->getSign($sign));
    }

    public function saveToCache($object, $id, $sign = null) {
        return PSCache::inst()->saveToCache($object, $id, $this->group, $this->getSign($sign));
    }

    public function clean() {
        PSCache::inst()->clean($this->group);
    }

}

?>