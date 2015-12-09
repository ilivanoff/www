<?php

abstract class BaseStockForm extends BaseAjaxForm {

    protected abstract function stockType();

    protected abstract function processStock(BaseStock $stock, PostArrayAdapter $adapter, $button);

    protected final function processImpl(PostArrayAdapter $adapter, $button) {
        return $this->processStock(StockManager::inst()->assertCanDoAction($adapter, $this->stockType()), $adapter, $button);
    }

    /** @var AjaxSuccess */
    public function getData() {
        return parent::getData();
    }

}

?>