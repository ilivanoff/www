/*
 * Редартор постов в базе
 */
PsJquery.onHrefClick('table.post_settings.dbedit .pscontrols a.confirm', {
    yes: function($a, id) {
        var $li = $a.parents('.post_settings:first');
        var name = $li.find('[name="name"]').val();
        var rubId = $li.find('[name="id_rubric"]').val();
                    
        var $processing = span_progress('Выполняем');
        $processing.insertAfter($a.hide());
            
        AdminAjaxExecutor.execute('PostEditAction', {
            ident: id,
            name: name,
            rubId: rubId,
            type: $li.data('type')
        }, function(res){
            $a.replaceWith(span_success(res));
            locationReload();
        }, function(err){
            $a.show();
            InfoBox.popupError(err, 3000);
        }, function(){
            $processing.remove();
        });
    }
});