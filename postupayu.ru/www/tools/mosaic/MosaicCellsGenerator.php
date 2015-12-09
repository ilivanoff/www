<?php

class MosaicCellsGenerator {

    private $CELLS = array();
    private $total;
    private $last = 0;

    function __construct($total) {
        $this->total = $total;
    }

    public function getCellNum() {
        ++$this->last;
        check_condition($this->last <= $this->total, 'More then max allowed cells requested.');

        $cellNum = 0;
        do {
            $cellNum = rand(1, $this->total);
        } while (array_key_exists($cellNum, $this->CELLS));


        $this->CELLS[$cellNum] = $this->last;
        return $cellNum;
    }

}

?>
