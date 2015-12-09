<?php

/**
 * Менеджер акций. Вё работает по следующему принципу:
 * 1. Каждая акция - это фолдинг stocks.
 * 2. Каждая акция прописывается в файле \resources\folded\idents\stock\stock.tpl в виде вот такого блока:
 *    {stock name='Мозайка' img='puzzle.png' ident='mosaic' to='2013-12-11 12:00:00' enabled='1' data_id='1'}
 * 3. В процессе обработки шаблона stock.tpl для каждой акции будет вызван метод {@link StockManager::registerStock}
 */
class StockManager extends StockResources {

    /** Html страницы со списком акций */
    private $HTML;

    /** Зарегистрированные акции */
    private $STOCKS;

    /** @return StockManager */
    private function INIT() {
        if (is_array($this->STOCKS)) {
            return $this;
        }

        $this->STOCKS = array();
        $this->HTML = IdentPagesManager::inst()->fetchTplImpl(IP_stock::getIdent(), array('SM' => $this));

        return $this;
    }

    /**
     * Возвращает все акции
     */
    private function getStocks() {
        return $this->INIT()->STOCKS;
    }

    /**
     * html загружаемой страницы со всеми проводимыми сейчас акциями
     */
    public function stockPageHtml() {
        return $this->INIT()->HTML;
    }

    /**
     * Метод, вызываемый блоком
     * {stock name='Мозайка' img='puzzle.png' ident='mosaic' to='2013-12-11 12:00:00' enabled='1' data_id='1'}
     * для регистрации акции
     * 
     * @param ArrayAdapter $params - параметры акции.
     */
    public function registerStock(ArrayAdapter $params) {
        $type = $params->str('type');

        if (!$this->existsEntity($type)) {
            return PsHtml::divErr("Неизвестный тип акции - [$type].");
        }

        if (!$this->hasAccess($type)) {
            return '';
        }

        /* @var $stock BaseStock */
        $stock = $this->getEntityClassInst($type, false);

        if (!$stock->isUserHasAccess()) {
            return '';
        }

        $stock->init($params);

        $type = $stock->getType();
        $ident = $stock->getStockIdent();

        if (array_key_exists($ident, $this->STOCKS)) {
            return PsHtml::divErr("Акция [$stock] уже зарегистрирована.");
        }

        $this->STOCKS[$ident] = $stock;
        $this->LOGGER->info("STOCK [$stock] is registered.");

        /*
         * Акция успешно зарегистрирована, покажем её "короткий" вид
         */
        $PARAMS['stock'] = $stock;
        $PARAMS['body'] = $stock->getShortView();
        return PSSmarty::template('common/stock.tpl', $PARAMS)->fetch();
    }

    /**
     * Проверяет, есть ли акции
     */
    public function hasStocks() {
        return count($this->getStocks()) > 0;
    }

    /**
     * ==================
     * = ЗАГРУЗКА АКЦИЙ =
     * ==================
     */

    /**
     * Загружает акцию по её параметрам. Может быть использован, если нужно обратиться к конкретной акции по её параметрам.
     * 
     * @param type $type - тип акции
     * @param array $params - параметры акции
     * @return BaseStock
     */
    public function getStockByParams($type, array $params = array()) {
        /* @var $stock BaseStock */
        foreach ($this->getStocks() as $stock) {
            if ($stock->isIt($type, $params)) {
                return $stock;
            }
        }
        raise_error("Акция с типом [$type] и указанными параметрами не зарегистрирована.");
    }

    /**
     * Загружает акцию по её идентификатору. Используется всеми базовыми методами.
     * 
     * @param array $ident - идентификатор акции (хэш от параметров акции, чтобы пользователь их лишний раз не видел)
     * @param type $type - тип акции (передаётся только для проверки, может быть null)
     * @return BaseStock
     */
    public function getStockByIdent($ident, $type = null) {
        $ident = $ident instanceof ArrayAdapter ? $ident->str(STOCK_IDENT_PARAM) : $ident;
        $stock = array_get_value($ident, $this->getStocks());
        check_condition($stock, "Акция с идентификатором [$ident] не зарегистрирована.");
        check_condition(!$type || ($stock->getType() == $type), "Акция [$stock] не принадлежит к типу [$type].");
        return $stock;
    }

    /**
     * Метод проверяет, можно ли в данной акции выполнить действие.
     * Его можно выполнить, если акция зарегистрирована и сейчас активна.
     * 
     * @param array $params - данные акции
     * @param type $type - идентификатор акции (для проверки)
     * @return BaseStock
     */
    public function assertCanDoAction($ident, $type = null) {
        $stock = $this->getStockByIdent($ident, $type);
        check_condition($stock->isActive(), "Акция [$stock] завершена.");
        return $stock;
    }

    /**
     * Метод вызывается для выполнения ajax-действий над акцией
     * 
     * @param AjaxSuccess
     */
    public function executeAjaxAction(ArrayAdapter $params) {
        $stock = $this->assertCanDoAction($params);
        $action = check_condition($params->str(STOCK_ACTION_PARAM), "Не передано действие для акции [$stock].");

        $method = 'ajax' . $action;
        check_condition(method_exists($stock, $method), "Действие [$action] не может быть выполнено для акции [$stock].");

        $this->LOGGER->info("Executing ajax action [$action] on stock [$stock] with params: " . $params);

        return $stock->$method($params);
    }

    /** @return StockManager */
    public static function inst() {
        return parent::inst();
    }

}

?>
