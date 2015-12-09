var PsStockManager = {
    logger: PsLogger.inst('PsStockManager').setDebug()/*.disable()*/,
    init: function() {
        var ident = defs[defs.STOCK_IDENT_PARAM];
        var logger = this.logger.logInfo('Инициализация. Идентификатор акции: {}.', ident);
        
        //Ко всем формам, добавляемым на данную popup страницу, добавим идентификатор акции
        $('form').livequery(function() {
            var $form = $(this);
            logger.logInfo('Добавляем идентификатор акции к форме '+$form.getFormId()+'.');
            $form.addFormHidden(defs.STOCK_IDENT_PARAM, ident);
        });

        this.sock = ident;
    },
    
    execute: function(action, data, callback, callbackErr, callbackAfter) {
        data = data || {};
        data[defs.STOCK_IDENT_PARAM] = this.sock;
        data[defs.STOCK_ACTION_PARAM] = action;
        AjaxExecutor.execute('StockAction', data, callback, callbackErr, callbackAfter);
    }
}

PsStockManager.init();