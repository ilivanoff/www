<?php

/*
 * popup - страница для отображения акций.
 * Сейчас только одна акция - мозайка, но по объекту stock мы можем всегда определить,
 * какая именно акция должна быть отображена.
 */

class PP_stock extends BasePopupPage {

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    /** @var BaseStock */
    private $stock;

    /** @var StockViewData */
    private $data;

    public function doProcess(ArrayAdapter $params) {
        $this->stock = StockManager::inst()->getStockByIdent($params);
        $this->data = $this->stock->getFullView();
    }

    public function getTitle() {
        return 'Акция: ' . $this->stock->getName();
    }

    public function getDescr() {
        return $this->getTitle();
    }

    public function getJsParams() {
        $params = to_array($this->data->getJsParams());
        //Всегда в параметрах js будет идентификатор текущей акции, для выполнения действия ajax
        $params[STOCK_IDENT_PARAM] = $this->stock->getStockIdent();
        return $params;
    }

    public function buildContent() {
        return $this->data->getHtml();
    }

    public function getSmartyParams4Resources() {
        
    }

}

?>