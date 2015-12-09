<?php

/**
 * Админская часть работы с маппингами.
 * Все методы модификации маппинга находятся именно здесь.
 *
 * @author azazello
 */
class AdminMappings extends Mappings {

    private $MAPPINGS;

    public function getAllMappings() {
        if (!is_array($this->MAPPINGS)) {
            $this->MAPPINGS = array();
            /** @var Mapping */
            foreach ($this->allMappings() as $mapping) {
                $this->MAPPINGS[$mapping->getHash()] = $mapping;
            }
        }
        return $this->MAPPINGS;
    }

    /** @return Mapping */
    public function getMapping($mhash) {
        return array_get_value($mhash, $this->getAllMappings());
    }

    public function saveMapping($mhash, $lident, array $ridents) {
        AdminMappingBean::inst()->saveMapping($mhash, $lident, $ridents);
    }

    public function cleanMapping($mhash) {
        AdminMappingBean::inst()->cleanMapping($mhash);
    }

    /**
     * 
     * Синглтон
     * 
     */
    private static $admin_inst;

    /** @return AdminMappings */
    public static function inst() {
        return self::$admin_inst = isset(self::$admin_inst) ? self::$admin_inst : new AdminMappings();
    }

}

?>
