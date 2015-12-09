<?php

/*
  CREATE TABLE `ps_mappings` (
  `mhash` char(32) CHARACTER SET latin1 NOT NULL,
  `lident` varchar(255) NOT NULL,
  `rident` varchar(255) NOT NULL,
  `ord` int(11) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 */

/**
 * Бин для работы с маппингами
 *
 * @author azazello
 */
class MappingBean extends BaseBean {

    public function getMappedEntitysDb($mhash, $lident) {
        return $this->getValues('select rident as value from ps_mappings where mhash=? and lident=? order by ord asc', array($mhash, $lident));
    }

    /** @return MappingBean */
    public static function inst() {
        return parent::inst();
    }

}

?>