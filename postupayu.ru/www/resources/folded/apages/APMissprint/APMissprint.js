/*
 * Удалить опечатку
 */
$(function() {
    PsJquery.onHrefClick($('.missprints .pscontrols a.delete'), {
        yes: function($a, id){
            var $progress = span_progress('Удаляем');
            $a.replaceWith($progress);
                    
            AdminAjaxExecutor.execute('MissprintAction', {
                id: id
            }, function(){
                $progress.extractParent('li').remove();
            }, $progress)
        }
    });
})
;
