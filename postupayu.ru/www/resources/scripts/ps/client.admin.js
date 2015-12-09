/*
 *  Ресурсы администратора в клиентской части
 */
var AdminClientManager = {
    executeAjax: function(data, callback, callbackErr, callbackAfter) {
        AjaxExecutor.execute('ADMINclient', data, callback, callbackErr, callbackAfter);
    }
}