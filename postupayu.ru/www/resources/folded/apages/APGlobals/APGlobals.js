/*
 * Глобальные настройки
 */

$(function(){
    var $BODY = $('.APGlobals');
    
    $BODY.find('table.editable').psEditableGrid({
        msg: 'Подтвердите сохранение глобальных настроек',
        reload: true,
        saver: function(CONTROLLER, onDone) {
            var data = CONTROLLER.models();
            var obj = {};
            data.walk(function(ob) {
                $.extend(obj, ob);
            });
            AdminAjaxExecutor.execute('SaveGlobalsAction', {
                globals: obj
            }, onDone, onDone);
        }
    });
    
});