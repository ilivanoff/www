<?php

class RubricsDataLoader extends IdIdentDataLoader {

    private $rp;

    public function __construct(RubricsProcessor $rp) {
        $this->rp = $rp;
    }

    protected function entityTitle() {
        return $this->rp->rubricTitle();
    }

    protected function loadEntitysLiteDB() {
        return $this->rp->dbBean()->getRubrics();
    }

    protected function loadEntitysFullDB(array $ids, $loadAll) {
        return $this->rp->dbBean()->getRubricsContent($ids, $loadAll);
    }

}

?>
