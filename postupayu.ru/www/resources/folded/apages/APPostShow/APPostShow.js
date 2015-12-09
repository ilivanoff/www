$(function() {
    PsJquery.onHrefClick('table.post_settings.visible .pscontrols a.confirm', {
        yes: function($a, id) {
            var $li = $a.parents('.post_settings:first');
            var shown = $li.find('[name="b_shown"]').isChecked() ? 1 : 0;
            var date = $li.find('[name="dt_pub"]').valEnsureIsNumber();
                    
            var processing = span_progress('Выполняем');
            $a.replaceWith(processing);
                
            AdminAjaxExecutor.execute('PostActivateAction', {
                id: id,
                date: date,
                show: shown,
                type: $li.data('type')
            }, locationReload, processing);
        }
    });
});