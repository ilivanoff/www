$(function() {
    var $BODY = $('.APRecEdit');

    var $controls = $BODY.find('.controls');
    var table = $BODY.find('table.database').data('table');
    var $btns = $controls.find('button');
    $btns.filter('.export').button({
        text: true,
        icons: {
            primary: 'ui-icon-arrowthickstop-1-n'
        }
    }).click(function() {
        PsDialogs.confirm($(this).attr('title'), function() {
            $btns.uiButtonDisable();
            AdminAjaxExecutor.execute('TableSettingsEdit', {
                action: 'dataExport',
                table: table
            } , function() {
                InfoBox.popupSuccess('Данные успешно экспортированы');
                locationReload();
            }, function(err) {
                InfoBox.popupError(err);
                $btns.uiButtonEnable();
            });
        });
    });
    
    $btns.filter('.import').button({
        text: true,
        icons: {
            primary: 'ui-icon-arrowreturnthick-1-w'
        }
    }).click(function() {
        PsDialogs.confirm($(this).attr('title'), function() {
            $btns.uiButtonDisable();
            AdminAjaxExecutor.execute('TableSettingsEdit', {
                action: 'acceptAllDiffs',
                table: table
            } , function() {
                InfoBox.popupSuccess('Данные успешно экспортированы');
                locationReload();
            }, function(err) {
                InfoBox.popupError(err);
                $btns.uiButtonEnable();
            });
        });
    });

    
    //Кнопки принятия изменений
    PsJquery.onHrefClick('.rec-control a.confirm', {
        msg: 'Принять изменения?',
        yes: function($a, id) {
            var $progress = span_progress('Сохраняем');
            $a.replaceWith($progress);
            AdminAjaxExecutor.execute('TableSettingsEdit', {
                action: 'acceptDiff',
                table: table,
                ident: id
            } , function() {
                $progress.replaceWith(span_success('Изменения успешно приняты'));
                locationReload();
            }, function(err){
                $progress.replaceWith(span_error(err));
            });
        }
    });
    
});