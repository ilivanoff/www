<?php

class AdminMappingBean extends MappingBean {

    public function saveMapping($mhash, $lident, array $ridents) {
        $this->update('delete from ps_mappings where mhash=? and lident=?', array($mhash, $lident));
        $ord = 0;
        foreach ($ridents as $rident) {
            $this->update('INSERT INTO ps_mappings (mhash, lident, rident, ord) VALUES (?, ?, ?, ?)', array($mhash, $lident, $rident, ++$ord));
        }
    }

    public function cleanMapping($mhash) {
        $this->update('delete from ps_mappings where mhash=?', $mhash);
    }

    /** @return AdminMappingBean */
    public static function inst() {
        return parent::inst();
    }

}

?>